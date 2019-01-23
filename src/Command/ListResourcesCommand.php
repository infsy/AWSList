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
		
		
		//
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
			foreach($result['ConfigurationRecordersStatus'] as $region) {
					$output->writeln('Recorded');
					
					// List EC2 resources
					$resourceType = 'AWS::EC2::Instance';
					$resultResource = $client->listDiscoveredResources(array(
					'resourceType' => $resourceType,
					'includeDeletedResources' => true,
					));
					$nbResult = 0;
					foreach($resultResource['resourceIdentifiers'] as $resource) {
						$nbResult++;
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
						$object->setAWSLastDetection(new \DateTime());
						$em->persist($object);
						$em->flush();
					}
					$output->writeln($nbResult . ' ' . $resourceType . ' in this Region');


					// List VPC resources
					$resourceType = 'AWS::EC2::VPC';
					$resultResource = $client->listDiscoveredResources(array(
					'resourceType' => $resourceType,
					'includeDeletedResources' => true,
					));
					$nbResult = 0;
					foreach($resultResource['resourceIdentifiers'] as $resource) {
						$nbResult++;
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
						$object->setAWSLastDetection(new \DateTime());
						$em->persist($object);
						$em->flush();
					}
					$output->writeln($nbResult . ' ' . $resourceType . ' in this Region');


					// List RDS resourcese
					$resourceType = 'AWS::RDS::DBInstance';
					$resultResource = $client->listDiscoveredResources(array(
					'resourceType' => $resourceType,
					'includeDeletedResources' => true,
					));
					$nbResult = 0;
					foreach($resultResource['resourceIdentifiers'] as $resource) {
						$nbResult++;
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
						$object->setAWSLastDetection(new \DateTime());
						$em->persist($object);
						$em->flush();
					}
					$output->writeln($nbResult . ' ' . $resourceType . ' in this Region');


					// List S3 resources
					$resourceType = 'AWS::S3::Bucket';
					$resultResource = $client->listDiscoveredResources(array(
					'resourceType' => $resourceType,
					'includeDeletedResources' => true,
					));
					$nbResult = 0;
					foreach($resultResource['resourceIdentifiers'] as $resource) {
						$nbResult++;
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
						$object->setAWSLastDetection(new \DateTime());
						$em->persist($object);
						$em->flush();
					}
					$output->writeln($nbResult . ' ' . $resourceType . ' in this Region');
			}
			}
		}
	}
}

