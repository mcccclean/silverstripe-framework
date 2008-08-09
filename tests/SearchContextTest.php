<?php

class SearchContextTest extends SapphireTest {
	static $fixture_file = 'sapphire/tests/SearchContextTest.yml';
	
	function testResultSetFilterReturnsExpectedCount() {
		$person = singleton('SearchContextTest_Person');
		$context = $person->getDefaultSearchContext();
		
		$results = $context->getResultSet(array('Name'=>''));
		$this->assertEquals(5, $results->Count());
		
		$results = $context->getResultSet(array('EyeColor'=>'green'));
		$this->assertEquals(2, $results->Count());
		
		$results = $context->getResultSet(array('EyeColor'=>'green', 'HairColor'=>'black'));
		$this->assertEquals(1, $results->Count());
		
	}
	
	function testSummaryIncludesDefaultFieldsIfNotDefined() {
		$person = singleton('SearchContextTest_Person');
		$this->assertContains('Name', $person->summary_fields());
		
		$book = singleton('SearchContextTest_Book');
		$this->assertContains('Title', $book->summary_fields());
	}
	
	function testAccessDefinedSummaryFields() {
		$company = singleton('SearchContextTest_Company');
		$this->assertContains('Industry', $company->summary_fields());
	}
	
	function testExactMatchUsedByDefaultWhenNotExplicitlySet() {
		 $person = singleton('SearchContextTest_Person');
		 $context = $person->getDefaultSearchContext();
		 
		 $this->assertEquals(
		 	array(
		 		"Name" => new ExactMatchFilter("Name"),
		 		"HairColor" => new ExactMatchFilter("HairColor"),
		 		"EyeColor" => new ExactMatchFilter("EyeColor")
		 	),
		 	$context->getFilters()
		 );
	}

	function testDefaultFiltersDefinedWhenNotSetInDataObject() {
		$book = singleton('SearchContextTest_Book');
		$context = $book->getDefaultSearchContext();
		
		 $this->assertEquals(
		 	array(
		 		"Title" => new ExactMatchFilter("Title")
		 	),
		 	$context->getFilters()
		 );	 
	}
	
	function testUserDefinedFiltersAppearInSearchContext() {
		//$company = singleton('SearchContextTest_Company');
		//$context = $company->getDefaultSearchContext();
				 
		/*$this->assertEquals(
			array(
				"Name" => new PartialMatchFilter("Name"),
		 		"Industry" => new ExactMatchFilter("Industry"),
		 		"AnnualProfit" => new PartialMatchFilter("AnnualProfit")
			),
			$context->getFilters()
		);*/
	}
	
	function testRelationshipObjectsLinkedInSearch() {
		//$project = singleton('SearchContextTest_Project');
		//$context = $project->getDefaultSearchContext();
		
		//$query = array("Name"=>"Blog Website");
		
		//$results = $context->getQuery($query);
	}
	
	function testCanGenerateQueryUsingAllFilterTypes() {
		$all = singleton("SearchContextTest_AllFilterTypes");
		$context = $all->getDefaultSearchContext();
		
		$params = array(
			"ExactMatch" => "Match Me Exactly",
			"PartialMatch" => "partially",
			"Negation" => "undisclosed"
		);
		
		$results = $context->getResults($params);
		
		$this->assertEquals(1, $results->Count());
		$this->assertEquals("Filtered value", $results->First()->HiddenValue);
	}
	
}

class SearchContextTest_Person extends DataObject implements TestOnly {
	
	static $db = array(
		"Name" => "Text",
		"Email" => "Text",
		"HairColor" => "Text",
		"EyeColor" => "Text"
	);
	
	static $searchable_fields = array(
		"Name", "HairColor", "EyeColor"
	);
	
}

class SearchContextTest_Book extends DataObject implements TestOnly {
	
	static $db = array(
		"Title" => "Text",
		"Summary" => "Text"
	);
	
}

class SearchContextTest_Company extends DataObject implements TestOnly {
	
	static $db = array(
		"Name" => "Text",
		"Industry" => "Text",
		"AnnualProfit" => "Int"
	);
	
	static $summary_fields = array(
		"Industry"
	);
	
	static $searchable_fields = array(
		"Name" => "PartialMatchFilter",
		"Industry" => "TextareaField",
		"AnnualProfit" => array("NumericField" => "PartialMatchFilter")
	);
	
}

class SearchContextTest_Project extends DataObject implements TestOnly {
	
	static $db = array(
		"Name" => "Text"
	);
	
	static $has_one = array(
		"Deadline" => "SearchContextTest_Deadline"	
	);
	
	static $has_many = array(
		"Actions" => "SearchContextTest_Action"
	);
	
	static $searchable_fields = array(
		"Name" => "PartialMatchFilter",
		"Actions.SolutionArea" => "ExactMatchFilter"
	);
	
}

class SearchContextTest_Deadline extends DataObject implements TestOnly {
	
	static $db = array(
		"CompletionDate" => "Datetime"
	);
	
	static $has_one = array(
		"Project" => "SearchContextTest_Project"	
	);
	
}

class SearchContextTest_Action extends DataObject implements TestOnly {
	
	static $db = array(
		"Description" => "Text",
		"SolutionArea" => "Text"
	);
	
	static $has_one = array(
		"Project" => "SearchContextTest_Project"	
	);
	
}

class SearchContextTest_AllFilterTypes extends DataObject implements TestOnly {
	
	static $db = array(
		"ExactMatch" => "Text",
		"PartialMatch" => "Text",
		"Negation" => "Text",
		"HiddenValue" => "Text"
	);
	
	static $searchable_fields = array(
		"ExactMatch" => "ExactMatchFilter",
		"PartialMatch" => "PartialMatchFilter",
		"Negation" => "NegationFilter"
	);
	
}

?>