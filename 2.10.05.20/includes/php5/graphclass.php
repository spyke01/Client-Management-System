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
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of the <organization> nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 ***************************************************************************/
include("includes/FusionCharts.php");
include("includes/FC_Colors.php");
 
class graphClass {
	// Graph settings
	public $graphWidth = 600;
	public $graphHeight = 300;
	public $numberPrefix = "";
	public $formatNumberScale = 0;
	public $decimalPrecision = 0;
	
	// Data used to build the graphs
	public $graphTitle = "";
	public $xAxisTitle = "";
	public $yAxisTitle = "";
	public $numOfSeries = 1;
	public $firstSeriesTitle = "";
	public $firstSeriesData = array();
	public $secondSeriesTitle = "";
	public $secondSeriesData = array();
	public $dataTitles = array();	

	//===============================================================
	// Our class constructor
	//===============================================================
	public function graphClass($title = "", $xTitle = "", $yTitle = "", $dTitles = "", $numSeries = 1, $fSTitle = "", $sSTitle = "") {
		$this->retitleGraph($title, $xTitle, $yTitle, $dTitles, $numSeries, $fSTitle, $sSTitle);
	}

	//===============================================================
	// Allows us to retitle a graph, it is also used by the 
	// class constructor
	//===============================================================
	public function retitleGraph($title = "", $xTitle = "", $yTitle = "", $dTitles = "", $numSeries = 1, $fSTitle = "", $sSTitle = "") {
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
	public function formatGraph($prefix = "", $numberScale = 0, $decPrecision = 0) {
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
	public function resizeGraph($width = 600, $height = 300) {
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
	public function addGraphData($fSData, $sSData = "") {
		$this->firstSeriesData = $fSData;
		$this->secondSeriesData = $sSData;
	}
	
	//===============================================================
	// This function returns our graph so that we can echo it or 
	// assign it to avariable 
	//
	// $divID = this is the id that will be used in the code
	//===============================================================
	public function buildGraph($divID, $graphtype = "column") {
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
		
		switch($graphtype) {
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