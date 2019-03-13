<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Aws\ConfigService\ConfigServiceClient;
use Aws\Ec2\Ec2Client;
use App\Entity\AWSObject;
use Yectep\PhpSpreadsheetBundle\Factory;

class ListResourcesCommand extends ContainerAwareCommand
{
	protected static $defaultName = 'app:list:resources';

	protected function configure()
	{
		$this
			->setName('app:list:resources')
			->setDescription('This list all of your AWS ressources')
			->addOption(
        			'export',
        			null,
        			InputOption::VALUE_OPTIONAL,
        			'If you want a CSV Export of the list',
        			false
    		);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		
		// initializing Container to work with Entities
		$em = $this->getContainer()->get('doctrine')->getManager();
		$em->getConnection()->getConfiguration()->setSQLLogger(null);
		
		// Déclaration of resources we want to check
		$listResources = array(
			"AWS::EC2::Instance",
			"AWS::EC2::VPC",
			"AWS::RDS::DBInstance",
			// "AWS::DynamoDB::Table",
			// "AWS::Lambda::Function",
			"AWS::IAM::User",
			// "AWS::EC2::Volume",
			// "AWS::EC2::EIP",
			// See https://docs.aws.amazon.com/cli/latest/reference/configservice/list-discovered-resources.html for complete list"
			"AWS::S3::Bucket");
		//
		$output->writeln('Type of object checked : ');
		print_r($listResources);	


// https://docs.aws.amazon.com/aws-sdk-php/v2/api/class-Aws.Sts.StsClient.html#_getFederationToken
$result = $client->assumeRole(array(
    // RoleArn is required
    'RoleArn' => 'arn:aws:iam::836114456886:role/ADFS-CAAProdWeb-Audit',
    // RoleSessionName is required
    'RoleSessionName' => 'cedric.thibault3@caaquebec.com',
    // 'Policy' => 'string',
    // 'DurationSeconds' => integer,
    'ExternalId' => 'cedric.thibault3@caaquebec.com',
    // 'SerialNumber' => 'string',
    // 'TokenCode' => 'string',
));

		// Get list of AWS Regions through EC2 describeRegions command
		$ec2client = Ec2Client::factory(array(
			'profile' => 'default',
			'version' => '2016-04-01',
			'region' => 'us-east-1'
		));
		$ec2result = $ec2client->describeRegions(array(
		));
		foreach($ec2result['Regions'] as $region) {
			$output->writeln('Check region '. $region['RegionName'] . ' : ');

			// Check if AWSConfig service is enabled for recording resources
			$client = ConfigServiceClient::factory(array(
				'profile' => 'default',
				'version' => '2014-11-12',
				'region' => $region['RegionName']
			));
			$result = $client->describeConfigurationRecorderStatus(array(
			));
			if (empty($result['ConfigurationRecordersStatus'])) {
				$output->writeln('Not recorded');	
			}
			else
			{
				$output->writeln('Recorded');
				foreach($listResources as $resourceType) {
					$resultResource = $client->listDiscoveredResources(array(
					'resourceType' => $resourceType,
					'includeDeletedResources' => true,
					));
					foreach($resultResource['resourceIdentifiers'] as $resource) {
						unset($object);
						$object = $em->getRepository('App:AWSObject')->findOneBy(
							['AWSId' => $resource['resourceId']]);
						// If we can't retrieve object in the db, we create a new one
						if (empty($object))
						{
							$object = new AWSObject();
							$object->setAWSFirstDetection(new \DateTime());
							$object->setAWSType($resource['resourceType']);
							$object->setAWSId($resource['resourceId']);
							$output->writeln('<error>Nouvelle resource '.$resource['resourceId'] . 'de type ' . $resource['resourceType'].'</error>');
						}
						// We add some info to the object
						if (!empty($resource['resourceName'])) {
							$object->setAWSName($resource['resourceName']);
						}
						if (!empty($resource['resourceDeletionTime'])) {
							$object->setAWSDeletionTime($resource['resourceDeletionTime']);
						}
						if (empty($object->getAWSRegion())) {
							$object->setAWSRegion($region['RegionName']);
						}
						if (empty($object->getAWSSubscription())) {
							// TODO: InsertLoop with subscriptions configured in .aws folder
							$object->setAWSSubscription('AWSLabs1');
						}
						$object->setAWSLastDetection(new \DateTime());
						$em->persist($object);
						$em->flush();
						
					} // End of foreach resource of a type recorded in the Region
				} // End of foreach ResourceType loop

				$resultCounts = $client->getDiscoveredResourceCounts(array(
				));
				foreach ($resultCounts['resourceCounts'] as $resourceCount) {
					if (in_array($resourceCount['resourceType'],$listResources)) {
						$output->writeln($resourceCount['count'] . ' ' . $resourceCount['resourceType'] . ' in this Region');
					}
				} // End of the loop to retrieve the count for each resource recorded and checked
			} // End of condition if Config is recording in the Region
		} //End of foreach Region loop
		
		// checking if need of csv export 
		$optionValue = $input->getOption('export');
		if ($optionValue) { 
			writeln('on exporte');
			$this->export();
		}
	}
	public function export(Factory $factory)
	{
		$spreadsheet = $factory->createSpreadsheet();
			// Set document properties
			$spreadsheet->getProperties()->setCreator('CAA')
				 ->setLastModifiedBy('CAA')
    			->setTitle('Scan result export')
    			->setSubject('Office 2007 XLSX Test Document')
    			->setDescription('Export of AWS objects found.')
    			->setKeywords('office 2007 openxml php')
    			->setCategory('Test result file');
    		$sheet = $spreadsheet->getActiveSheet();
			$sheet->setTitle('AWS Objects');
			$sheet->setCellValue('A1', 'List of objects from AWS default access keys');
			$ligne = 3;
			
			$objects = $em->getRepository('App:AWSObject')->findAll();
			foreach ($objects as $AWSObject) {
							$sheet->setCellValue('A'.$ligne, $AWSObject->getAWSId());
							$sheet->setCellValue('B'.$ligne, $AWSObject->getAWSType());
							if (!empty($AWSObject->getAWSName())) {
								$sheet->setCellValue('C'.$ligne, $objects->getAWSName());
							}
							if (!empty($AWSObject->getAWSDeletionTime())) {
								$sheet->setCellValue('D'.$ligne, $objects->getAWSDeletionTime());
							}
							if (!empty($AWSObject->getAWSRegionName())) {
								$sheet->setCellValue('E'.$ligne, $objects->getAWSRegionName());
							}
							
							$ligne++; 
				
			}
		$writerXlsx = $this->get('phpoffice.spreadsheet')->createWriter($spreadsheet, 'Xlsx');
		$writerXlsx->save('./exportAWSObjects.xlsx');
	}
}

