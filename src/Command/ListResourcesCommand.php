<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Aws\ConfigService\ConfigServiceClient;
use Aws\Ec2\Ec2Client;
use App\Entity\AWSObject;

class ListResourcesCommand extends ContainerAwareCommand
{
	protected static $defaultName = 'app:list:resources';

	protected function configure()
	{
		$this
			->setName('app:list:resources')
			->setDescription('This list all of your AWS ressources');
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
		// Get list of AWS Regions through EC2 describeRegions command
		$ec2client = Ec2Client::factory(array(
			'profile' => 'awslabs1',
			'version' => '2016-04-01',
			'region' => 'us-east-1'
		));
		$ec2result = $ec2client->describeRegions(array(
		));
		foreach($ec2result['Regions'] as $region) {
			$output->writeln('Check region '. $region['RegionName'] . ' : ');

			// Check if AWSConfig service is enabled for recording resources
			$client = ConfigServiceClient::factory(array(
				'profile' => 'awslabs1',
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
	}
}

