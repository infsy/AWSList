<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Aws\ConfigService\ConfigServiceClient;
use Aws\Ec2\Ec2Client;

class ListResourcesCommand extends Command
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
				if ($region['recording']) {
					$output->writeln('Recorded');
					
					// List EC2 resources
					$resultResource = $client->listDiscoveredResources(array(
					'resourceType' => 'AWS::EC2::Instance',
					'includeDeletedResources' => true,
					));
					foreach($resultResource['resourceIdentifiers'] as $resource) {
						$output->writeln('resourceType : '.$resource['resourceType']);
						$output->writeln('resourceId : '.$resource['resourceId']);
					}
					// List VPC resources
					$resultResource = $client->listDiscoveredResources(array(
					'resourceType' => 'AWS::EC2::VPC',
					'includeDeletedResources' => true,
					));
					foreach($resultResource['resourceIdentifiers'] as $resource) {
						$output->writeln('resourceType : '.$resource['resourceType']);
						$output->writeln('resourceId : '.$resource['resourceId']);
					}
					// List RDS resources
					$resultResource = $client->listDiscoveredResources(array(
					'resourceType' => 'AWS::RDS::DBInstance',
					'includeDeletedResources' => true,
					));
					foreach($resultResource['resourceIdentifiers'] as $resource) {
						$output->writeln('resourceType : '.$resource['resourceType']);
						$output->writeln('resourceId : '.$resource['resourceId']);
					}
					// List S3 resources
					$resultResource = $client->listDiscoveredResources(array(
					'resourceType' => 'AWS::S3::Bucket',
					'includeDeletedResources' => true,
					));
					foreach($resultResource['resourceIdentifiers'] as $resource) {
						$output->writeln('resourceType : '.$resource['resourceType']);
						$output->writeln('resourceId : '.$resource['resourceId']);
					}
				}
				else {
					$output->writeln('Not recorded');
				}
			}
			}
		}
	}
}

