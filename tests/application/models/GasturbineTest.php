<?php

require_once('../application/models/DbTable/Gasturbine.php');

class Model_GasturbineTest extends PHPUnit_Extensions_Database_TestCase
{
	protected $testData;	
		
	protected function getConnection()
    {
        $pdo = new PDO('mysql:host=localhost;dbname=hivetest', 'root', 'reason');
        return $this->createDefaultDBConnection($pdo, 'hivetest');
		
    }
	
	/* provides seed/initital data to the database */
 
    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__).'/../xmldataset/gasturbine/gasturbineDataSet.xml');
    }
	
	/*	Provides the required data for use within this class */
	
	public function dataProvider()
	{
		return array(
		array(	'GTId'				=>	'1',
				'GTName'			=>	'GT0',
				'GTModelNum'		=>	'v93.4A',
				'plantId'			=>	'113',
				'EOHDate'			=>	'2011-12-08',
				'EOH'				=>	'213',
				'numStarts'			=>	'23',
				'numTrips'			=>	'2',
				'minorInspInter'	=>	'3',
				'HGPIInspInter'		=>	'3',
				'EHGPIInspInter'	=>	'3',
				'nextMinor'			=>	'2011-12-08',
				'nextMajor'			=>	'2011-10-25',
				'timeupdate'		=>	'2012-01-14 01:10:39'),
				
		array(	'GTId'				=>	'3',
				'GTName'			=>	'GTA',
				'GTModelNum'		=>	'v93.4A',
				'plantId'			=>	'110',
				'EOHDate'			=>	'2011-12-08',
				'EOH'				=>	'213',
				'numStarts'			=>	'23',
				'numTrips'			=>	'2',
				'minorInspInter'	=>	'3',
				'HGPIInspInter'		=>	'3',
				'EHGPIInspInter'	=>	'3',
				'nextMinor'			=>	'2011-12-08',
				'nextMajor'			=>	'2011-10-25',
				'timeupdate'		=>	'2012-01-14 01:10:39')

		);	
			
	}
	
	/* Add data for new gt */
	
	public function addDataProvider()
	{
		return array(
				'GTId'				=>	'0',
				'GTName'			=>	'GT Add Again',
				'GTModelNum'		=>	'v93.4C',
				'plantId'			=>	'110',
				'EOHDate'			=>	'2011-12-08',
				'EOH'				=>	'9',
				'numStarts'			=>	'9',
				'numTrips'			=>	'9',
				'minorInspInter'	=>	'9',
				'HGPIInspInter'		=>	'9',
				'EHGPIInspInter'	=>	'9',
				'nextMinor'			=>	'2012-02-05',
				'nextMajor'			=>	'2011-10-25',
				'timeupdate'		=>	'2012-01-14 01:10:39');
	}
	
	/* Constructor initialization
	 * Case where the gtid value is not passed
	 * Must return null as value
	 */
	
	public function testConstructorWithoutParameters()
	{
		/* Gets the test data */
		
		$this->testData = $this->dataProvider();
	
		$gt = new Model_DbTable_Gasturbine(Zend_Db_Table::getDefaultAdapter());
		
		$gtName = $gt->getGTName();
		$this->assertEquals(null,$gtName); //Assert will return true
	}
	
	/* Constructor Initialization
	 * Case where the gtid value is passed to the constructor
	 * This case checks all the get functions
	 */
	
	public function testConstructorWithParameters()
	{
		/* Gets the test data */
		
		$testData = $this->dataProvider();

		$gt = new Model_DbTable_Gasturbine(Zend_Db_Table::getDefaultAdapter(),3); //gtid = 3
		
		$returnData = $gt->getGTData();
		$this->assertEquals($testData[1],$returnData);
	}	
	
	/* This is a test for all the get methods that are available
	 * 	1.getGTID()
	 * 	2.getGTName()
	 * 	3.getGTModelNum()
	 * 	4.getPlantId
	 * 	5.getLastUpdate()
	 * 	6.getGTData()
	 * 	7.getList()
	 */
	
	public function testGetMethods()
	{
		/*gets the test data */
		
		$testData = $this->dataProvider();
	
		$gt = new Model_DbTable_Gasturbine(Zend_Db_Table::getDefaultAdapter(),1); //gtid = 3
		
		/*get data */
		
		$GTId = $gt->getGTId();
		$GTName = $gt->getGTName();
		$GTModelNum = $gt->getGTModelNum();
		$plantId = $gt->getPlantId();
		$timeupdate = $gt->getLastUpdateTime();
		
		/*	assert data, All assert will return true */
		
		$this->assertEquals($GTId,$testData[0]['GTId']);
		$this->assertEquals($GTName,$testData[0]['GTName']);
		$this->assertEquals($GTModelNum,$testData[0]['GTModelNum']);
		$this->assertEquals($plantId,$testData[0]['plantId']);
		$this->assertEquals($timeupdate,$testData[0]['timeupdate']);
		 
		/* get data in array format , will assert true */
		
		$returnData = $gt->getGTData();
		$this->assertEquals($testData[0],$returnData);
		
		/* Tests the getList method
		 * 
		 * The first case does not give a where input , it must return complete list
		 * 
		 * The second test case pases a specific value for where
		 * the value passed to are - GTId , plantId
		 * 
		 * The Third test case passes multiple values to the where
		 * the values passed are - GTID,plantId,timeupdate
		 */
		
		/* No where input */
		
		
		/* single input */ 
		
		$gtList = Model_DbTable_Gasturbine::getList(array('columns' => array('GTId' => 1)));
		$this->assertEquals(1,count($gtList));
		$this->assertEquals($testData[0],$gtList[0]);
		
		$gtList = Model_DbTable_Gasturbine::getList(array('columns' => array('plantId' => 110)));
		$this->assertEquals(2,count($gtList));
		$this->assertEquals($testData[1],$gtList[1]);
		
		/* Multiple conditions */
		
		$gtList = Model_DbTable_Gasturbine::getList(array('columns' => array('plantId' => 110, 'numStarts' => 23)));
		$this->assertEquals(2,count($gtList));
		$this->assertEquals($testData[1],$gtList[1]);
		
	}
	
	/* This is a test for all the get methods that are available
	 * 	1.setGTName()
	 * 	2.setGTModelNum()
	 *  3.setPlantId()
	 * 	4.setGTData()
	 */
	
	public function testSetMethods()
	{
		/* Gets the test data */
		
		$testData = $this->dataProvider();
		
		$gt = new Model_DbTable_Gasturbine(Zend_Db_Table::getDefaultAdapter(),3); //gtid = 3
		
		/* Get the current GTName and assert true */
		
		$this->assertEquals('GTA',$gt->getGTName());
		$this->assertEquals('v93.4A',$gt->getGTModelNum());
		$this->assertEquals('110',$gt->getPlantId());
		
		/* Changed data for the gas turbine */
		$gtNameChange = 'GT Test';
		$gtModelNumChange = 'v93.4B';
		$plantIdChange = '114';
		
		/* Change to new Data */
		
		$gt->setGTName($gtNameChange);
		$gt->setGTModelNum($gtModelNumChange);
		$gt->setPlantId($plantIdChange);
		
		/* get the new data for the gasturbine and assert if it has changed */
		$this->assertEquals($gtNameChange,$gt->getGTName());
		$this->assertEquals($gtModelNumChange,$gt->getGTModelNum());
		$this->assertEquals($plantIdChange,$gt->getPLantId());
		
		/*test for setGTData */
		
		$returnData = $gt->getGTData();
		$returnData['GTName'] = 'GTData Test';
		$returnData['GTModelNum'] = 'v93.4C';
		$returnData['plantId'] = '115';
		
		$gt->setGTData($returnData);
		
		$changedData = $gt->getGTData();
		$this->assertEquals($returnData,$changedData);
		
		
		
	}
	
	/* This is a test for the Save method that writes
	 * the changed contents into the database.
	 * An xml assert is done to make sure that the 
	 * current database state is the same as the gasturbineAfterSave.xml 
	 */
	
	public function testSaveMethod()
	{
		$gt = new Model_DbTable_Gasturbine(Zend_Db_Table::getDefaultAdapter(),3); //gtid = 3
		
		/* Gets the data for gt with id =3 from database and compare it with xml
		 * gastrubineBeforeSave.xml
		 */
		
		$ds = new PHPUnit_Extensions_Database_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('gasturbines', 'SELECT * FROM gasturbines WHERE GTId = 3');
		
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/../xmldataset/gasturbine/gasturbineBeforeSave.xml"),
            $ds
        );
		
		/* Gets the data for gt with id = 3 and saves it into database */
		
		$returnData = $gt->getGTData();
		$returnData['GTName'] = 'GTData Test';
		$returnData['GTModelNum'] = 'v93.4C';
		$returnData['plantId'] = '115';
		$returnData['EOH'] = '50';
		$returnData['numStarts'] = '60';
		$returnData['numTrips'] = '70';
		$returnData['nextMinor'] = '2012-02-05';
		
		$gt->setGTData($returnData);
		$gt->save();
		
		/* Gets the data for the same gt and compares with dataset
		 * gasturbineAfterSave.xml to assert if data is saved
		 */
		
		$ds->addTable('gasturbines', 'SELECT * FROM gasturbines WHERE GTId = 3');
		
        $this->assertDataSetsEqual(
            $this->createFlatXmlDataSet(dirname(__FILE__)
                                      . "/../xmldataset/gasturbine/gasturbineAfterSave.xml"),
            $ds
        );
		
	} 

	/* This is to test the delete method that deletes a gt from the database
	 * the database is compared with gastrubineAfterDelete.xml
	 */
	
	public function testDeleteMethod()
	{
		 $gt = new Model_DbTable_Gasturbine(Zend_Db_Table::getDefaultAdapter(),3); //gtid = 3
 		
		 $gt->del();
		
		
		$ds = new PHPUnit_Extensions_Database_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('gasturbines', 'SELECT * FROM gasturbines WHERE GTId = 3');
		
		$query = 'SELECT * FROM gasturbines WHERE GTId = 3';
		
		$d = new PHPUnit_Extensions_Database_DataSet_QueryTable('gasturbines',$query,$this->getConnection());
		$num = $d->getRowCount();
		$this->assertEquals(0,$num);

	}

	/* This test is to a new GT , initially the id will be 0
	 * once gt is added to database it will return the GTId
	 * which will then be set
	 */
	
	public function testAddingNewGT()
	{
		$gt = new Model_DbTable_Gasturbine(Zend_Db_Table::getDefaultAdapter());
		$this->assertEquals(0,$gt->getGTId());
		
		/* Set just one parameter */
		
		// $gt->setGTName('Add GT');
		// $gt->setGTData();
		// $gt->save();
		
		/* Set all parameter */

		$setData = $this->AddDataProvider();
		$gt->setGTData($setData);
		$gt->save();
		
		/* get gtid after insert */
		
		$insertId = $gt->getGTId();
		
		 $ds = new PHPUnit_Extensions_Database_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('gasturbines', 'SELECT * FROM gasturbines WHERE GTId = '.$insertId);
		
        $this->assertDataSetsEqual(
             $this->createFlatXmlDataSet(dirname(__FILE__)
                                       . "/../xmldataset/gasturbine/gasturbineAddNewGT.xml"),
   			$ds
         );
	}

	
}
