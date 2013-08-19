<?php

class Statistics_Lib_Manage
{
	public static function getByPeriod(DateTime $start, DateTime $end)
	{
		$client = Zend_Gdata_ClientLogin::getHttpClient('', '', Zend_Gdata_Analytics::AUTH_SERVICE_NAME);
		$service = new Zend_Gdata_Analytics($client);
		
		$query = $service->newDataQuery()
				->setProfileId('60007439')
				->addMetric(Zend_Gdata_Analytics_DataQuery::METRIC_VISITORS)
				->addMetric(Zend_Gdata_Analytics_DataQuery::METRIC_VISITS)
				->addMetric(Zend_Gdata_Analytics_DataQuery::METRIC_NEW_VISITS)
				->addMetric('ga:percentNewVisits')
				->setStartDate($start->format('Y-m-d'))
				->setEndDate($end->format('Y-m-d'));
		
		$result = $service->getDataFeed($query); 
		$row = $result[0];
		
		$datas = array(
			'visitors'  => $row->getMetric(Zend_Gdata_Analytics_DataQuery::METRIC_VISITORS)->getValue(),
			'visits'    => $row->getMetric(Zend_Gdata_Analytics_DataQuery::METRIC_VISITS)->getValue(),
			'newVisits' => $row->getMetric(Zend_Gdata_Analytics_DataQuery::METRIC_NEW_VISITS)->getValue(),
			'percentNewVisits'  => round($row->getMetric('ga:percentNewVisits')->getValue(), 0)
		);
		
		return $datas;
	}
	
	public static function getToday()
	{
		$date = new DateTime();
        return self::getByPeriod($date, $date);
	}
	
	public static function getLastMonth()
	{
		$end 	= new DateTime();
		
		$start = clone $end;
		$start->sub(new DateInterval('P1M'));
		
        return self::getByPeriod($start, $end);
	}
}