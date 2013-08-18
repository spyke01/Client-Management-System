<?php
/***************************************************************************
 *                               graphclass.php
 *                            -------------------
 *   begin                : Tuseday, October 31, 2008
 *   copyright            : (C) 2008 Fast Track Sites
 *   email                : sales@fasttracksites.com
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 * This program is licensed under the Fast Track Sites Program license 
 * located inside the license.txt file included with this program. This is a 
 * legally binding license, and is protected by all applicable laws, by 
 * editing this page you fall subject to these licensing terms.
 *
 ***************************************************************************/
include("includes/FusionCharts.php");
include("includes/FC_Colors.php");
 
class graphClass {
	// Graph settings
	var $graphWidth = 600;
	var $graphHeight = 300;
	var $numberPrefix = "";
	var $formatNumberScale = 0;
	var $decimalPrecision = 0;
	
	// Data used to build the graphs
	var $graphTitle = "";
	var $xAxisTitle = "";
	var $yAxisTitle = "";
	var $numOfSeries = 1;
	var $firstSeriesTitle = "";
	var $firstSeriesData = array();
	var $secondSeriesTitle = "";
	var $secondSeriesData = array();
	var $dataTitles = array();	

	//===============================================================
	// Our class constructor
	//===============================================================
	function graphClass($title = "", $xTitle = "", $yTitle = "", $dTitles = "", $numSeries = 1, $fSTitle = "", $sSTitle = "") {
		$this->retitleGraph($title, $xTitle, $yTitle, $dTitles, $numSeries, $fSTitle, $sSTitle);
	}

	//===============================================================
	// Allows us to retitle a graph, it is also used by the 
	// class constructor
	//===============================================================
	function retitleGraph($title = "", $xTitle = "", $yTitle = "", $dTitles = "", $numSeries = 1, $fSTitle = "", $sSTitle = "") {
		$this->graphTitle = $title;
		$this->xAxisTitle = $xTitle;
		$this->yAxisTitle = $yTitle;
		$this->dataTitles = $dTitles;
		$this->numOfSeries = $numSeries;
		$this->firstSeriesTitle = $fSTitle;
		$this->secondSeriesTitle = $sSTitle;
	}

	//===============================================================
	// Allows us to change the various data formating options 
	// of our graph
	//===============================================================
	function formatGraph($prefix = "", $numberScale = 0, $decPrecision = 0) {
		$this->numberPrefix = $prefix;
		$this->formatNumberScale = $numberScale;
		$this->decimalPrecision = $decPrecision;
	}
	
	//===============================================================
	// This function resizes our graph 
	//
	// $width = new width of our graph
	// $height = new height of our graph
	//===============================================================
	function resizeGraph($width = 600, $height = 300) {
		$this->graphWidth = $width;
		$this->graphHeight = $height;
	}
	
	//===============================================================
	// This function adds the actual data to our graph 
	//
	// $fSData = first data set of graph
	// $sSData = second data set of graph (only used in Multi-
	//			 Series Grpahs)
	//===============================================================
	function addGraphData($fSData, $sSData = "") {
		$this->firstSeriesData = $fSData;
		$this->secondSeriesData = $sSData;
	}
	
	//===============================================================
	// This function returns our graph so that we can echo it or 
	// assign it to avariable 
	//
	// $divID = this is the id that will be used in the code
	//===============================================================
	function buildGraph($divID, $graphType = "column") {
		// Declare our variables
		$strXML = "";
		$msPrefix = "";
		$graphFile = "";
		
		//===================================================
		// Build our XML
		//===================================================
		// Build graph XML item with our settings
		$strXML = "<graph caption='" . $this->graphTitle . "' xAxisName='" . $this->xAxisTitle . "' yAxisName='" . $this->yAxisTitle . "' numberPrefix='" . $this->numberPrefix . "' formatNumberScale='" . $this->formatNumberScale . "' decimalPrecision='" . $this->decimalPrecision . "'>";
		
		// Determine if we are working with more than 1 series
		if ($this->numOfSeries == 1) {		
			// Build our data sets
			foreach ($this->firstSeriesData as $key => $data) {
				$strXML .= "	<set name='" . $this->dataTitles[$key] . "' value='" . $data ."' color='". getFCColor() ."' />";
			}
		}
		else {
			// Build our Categories
			$strXML .= "	<categories>";
			
			foreach ($this->dataTitles as $key => $title) {
        		$strXML .= "		<category name='" . $title . "' />";
			}
			
			$strXML .= "	</categories>";
			
			// Build our first series
			$strXML .= "	<dataset seriesName='" . $this->firstSeriesTitle . "' color='AFD8F8'>";
			
			foreach ($this->firstSeriesData as $key => $data) {
        		$strXML .= "		<set value='" . $data . "' />";
			}
			
			$strXML .= "	</dataset>";
			
			// Build our second series
			$strXML .= "	<dataset seriesName='" . $this->secondSeriesTitle . "' color='F6BD0F'>";
			
			foreach ($this->secondSeriesData as $key => $data) {
        		$strXML .= "		<set value='" . $data . "' />";
			}
			
			$strXML .= "	</dataset>";
			
		}
		
		// Close our graph XML
		$strXML .= "</graph>";
	
		//===================================================
		// Build our graph
		//===================================================
		$msPrefix = ($this->numOfSeries == 1) ? "" : "MS";
		
		switch($graphType) {
			case "area2d":
				$graphFile = "FCF_" . $msPrefix . "Area2D.swf";
				break;
			case "bar2d":
				$graphFile = "FCF_" . $msPrefix . "Bar2D.swf";
				break;
			case "column":
				$graphFile = "FCF_" . $msPrefix . "Column3D.swf";
				break;
			case "column2d":
				$graphFile = "FCF_" . $msPrefix . "Column2D.swf";
				break;
			case "doughnut2d":
				$graphFile = "FCF_Doughnut2D.swf";
				break;
			case "funnel":
				$graphFile = "FCF_Funnel.swf";
				break;
			case "line":
				$graphFile = "FCF_" . $msPrefix . "Line.swf";
				break;
			case "pie":
				$graphFile = "FCF_Pie3D.swf";
				break;
			case "pie2d":
				$graphFile = "FCF_Pie2D.swf";
				break;
			default:
				$graphFile = "FCF_" . $msPrefix . "Column3D.swf";
				break;
		}
		
		return renderChart("FusionCharts/" . $graphFile, "", $strXML, $divID, $this->graphWidth, $this->graphHeight, false, false);
	}
} 

?>