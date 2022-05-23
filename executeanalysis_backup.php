
<?php

//INPUT VARIABLES
$analysisType = $_POST['analysisType'];//BASED ON WHICH EXECUTE BUTTON USER CLICKED
$assetClass = $_POST['assetClass'];//GOLD OR CRUDE?
$fundamentaModel = $_POST['fundamentaModel'];//FUNDAMENTAL MODEL TO RUN IN FUNDAMENTAL.py FILE
$fundamentalOffsetYears = $_POST['fundamentalOffsetYears'];//FUNDAMENTAL OFFSET YEARS
$technicalModel = $_POST['technicalModel'];//MODEL TO RUN IN TECHNICAL.py
$technicalSmaFast = $_POST['technicalSmaFast'];//TECHICAL.py DYN VAR
$technicalSmaSlow = $_POST['technicalSmaSlow'];//TECHICAL.py DYN VAR
$technicalEma = $_POST['technicalEma'];//TECHICAL.py DYN VAR
$technicalVolWindow = $_POST['technicalVolWindow'];//TECHICAL.py DYN VAR
$macroModel = $_POST['macroModel'];//MACRO MODEL TO RUN IN MACRO.py FILE
$macroHiddenLayers = $_POST['macroHiddenLayers'];//MACRO.py DYN VAR
$macroNodesPerLayer = $_POST['macroNodesPerLayer'];//MACRO.py DYN VAR
$aggModel = $_POST['aggModel'];//Aggregator MODEL TO RUN IN Aggregator.py FILE



if($assetClass == "gold")
{
    $csvFile = "Gold.csv";
}
else
{
    $csvFile = "Crude.csv";
}



//FUNDAMENTAL
$dataArray = array("assetClass"=>$assetClass, "csvFile"=>$csvFile, "model"=>$fundamentaModel, "analysisType"=>$analysisType);
$dataArray = json_encode($dataArray);
$outputFundamental=shell_exec("python py/fundamental.py "  . base64_encode(json_encode($dataArray)));
//OUTPUTS   
$json = json_decode($outputFundamental, TRUE);
$reportTitles = $json["ReportTitles"];
$reportData = $json["reports"];
$plots = $json["plots"];
$fundDfs = $json["dataFrame"];


echo $fundDfs;









//TECHNICAL
//CSV MOD FOR TECH ANALYSIS
if($assetClass == "gold")
{
    $csvFile = "gold_ochl.csv";
}
else
{
    $csvFile = "crude_ochl.csv";
}

$dataArray = array("assetClass"=>$assetClass, "csvFile"=>$csvFile, "model"=>$technicalModel, "analysisType"=>$analysisType, "technicalModel"=>$technicalModel, "technicalSmaFast"=>$technicalSmaFast, "technicalSmaSlow"=>$technicalSmaSlow, "technicalEma"=>$technicalEma, "technicalVolWindow"=>$technicalVolWindow);
$dataArray = json_encode($dataArray);
$outputTechnical=shell_exec("python py/technical.py "  . base64_encode(json_encode($dataArray)));
//OUTPUTS  
$jsonTech = json_decode($outputTechnical, TRUE);
$reportTitlesTech = $jsonTech["ReportTitles"];
$reportDataTech = $jsonTech["reports"];
$plotsTech = $jsonTech["plots"];
$techDfs = $jsonTech["dataFrame"];






//MACRO
$dataArray = array("assetClass"=>$assetClass, "csvFile"=>$csvFile, "model"=>$technicalModel, "analysisType"=>$analysisType, "macroModel"=>$macroModel, "macroHiddenLayers"=>$macroHiddenLayers, "macroNodesPerLayer"=>$macroNodesPerLayer);
$dataArray = json_encode($dataArray);
$outputMacro=shell_exec("python py/macro.py "  . base64_encode(json_encode($dataArray)));
//OUTPUTS  















?>

    <script>
      
        //FUNDAMENTAL
        let fundamentalResponse = JSON.stringify(<?php echo $outputFundamental?>);
        fundamentalResponse = JSON.parse(fundamentalResponse);
        let fundamentalReports = fundamentalResponse.reports; //FUNDAMENTAL REPORTS
            




    </script>



    <!-- FUNDAMENTAL-->
    <!-- DATAFRAMES-->
    <div class='executionResults-heading'>
        Dataframes - Fundamental
    </div>
    <div class='executionResults-graphs'>
        
    <?php
            $funPlotCounter = 0;
            foreach($fundDfs as $fundDf)
            {
                
                ?>
                    <div class='exec-eachPlot' id='exec-fundamental-eachDf<?php echo $funPlotCounter?>'>
                        
                        <div class='dfWrap'>

                            <?php echo $fundDf[$funPlotCounter]?>

                        </div>
                        
                    
                    </div>
                <?php
                $funPlotCounter++;
            }
            
        ?>


    </div>
    <!-- REPORTS-->
    <div class='executionResults-heading'>
        Reports - Fundamental
    </div>
    <div class='executionResults-graphs'>

        <?php
            $reportCounter = 0;
            foreach($reportTitles as $title)
            {
                
                ?>
                    <div class='exec-eachPlot' id='exec-fundamental-eachRep<?php echo $reportCounter?>'>
                        <span class='eachRep-title'><?php echo $title?></span>

                        <?php echo $reportData[$reportCounter]?>
                    
                    </div>
                <?php
                $reportCounter++;
            }
            
        ?>


    </div>


    <!-- PLOTS-->
    <div class='executionResults-heading'>
        Plots - Fundamental
    </div>

    <div class='executionResults-graphs'>
        <?php
            $plotFundCounter = 0;
            foreach($plots as $fundPlot)
            {
                $html=file_get_contents("graphs/".$fundPlot);
                ?>
                    <div class='exec-eachPlot' id='exec-eachPlot-fund<?php echo $plotFundCounter?>'>
                        <?php echo $html?>
                    </div>
                <?php
                $plotFundCounter++;
            }
        ?>
    </div>








    <!-- TECHNICAL-->
       <!-- DATAFRAMES-->
       <div class='executionResults-heading'>
        Dataframes - Technical
    </div>
    <div class='executionResults-graphs'>
        
    <?php
            $dfCounterTech = 0;
            foreach($techDfs as $tdf)
            {
                
                ?>
                    <div class='exec-eachPlot' id='exec-tech-eachDf<?php echo $dfCounterTech?>'>
                        
                        <div class='dfWrap'>

                            <?php echo $techDfs[$dfCounterTech]?>

                        </div>
                        
                    
                    </div>
                <?php
                $dfCounterTech++;
            }
            
        ?>


    </div>
    <!-- REPORTS-->
    <div class='executionResults-heading'>
        Reports - technical
    </div>
    <div class='executionResults-graphs'>
        <?php
            $reportCounter = 0;
            foreach($reportTitlesTech as $title)
            {
                
                ?>
                    <div class='exec-eachPlot' id='exec-fundamental-eachRep<?php echo $reportCounter?>'>
                        <span class='eachRep-title'><?php echo $title?></span>

                        <?php echo $reportDataTech[$reportCounter]?>
                    
                    </div>
                <?php
                $reportCounter++;
            }
            
        ?>


    </div>


    <!-- PLOTS-->
    <div class='executionResults-heading'>
        Plots - Technical
    </div>

    <div class='executionResults-graphs'>
        <?php
            $plotTechCounter = 0;
            foreach($plotsTech as $Techplot)
            {
                
                $html=file_get_contents("graphs/".$Techplot);

                ?>
                    <div class='exec-eachPlot' id='exec-eachPlot-tech<?php echo $plotTechCounter?>'>
                    
                    </div>
                <?php
                $plotTechCounter++;
            
            }
        ?>
    </div>













    
    <!-- MACRO-->
    <!-- REPORTS-->
    <div class='executionResults-heading'>
        Reports - MACRO
    </div>
    <div class='executionResults-graphs'>
        <?php
            $reportCounter = 0;
            foreach($reportTitlesTech as $title)
            {
                
                ?>
                    <div class='exec-eachPlot' id='exec-macro-eachRep<?php echo $reportCounter?>'>
                        <span class='eachRep-title'><?php echo $title?></span>

                        <?php echo $reportDataTech[$reportCounter]?>
                    
                    </div>
                <?php
                $reportCounter++;
            }
            
        ?>


    </div>


    <!-- PLOTS-->
    <div class='executionResults-heading'>
        Plots - MACRO
    </div>

    <div class='executionResults-graphs'>
        <?php
            $plotCounter = 0;
            foreach($plotsTech as $plot)
            {
                
                $html=file_get_contents("graphs/".$plot);
                ?>
                    <div class='exec-mac-plot' id='exec-eachPlot-mac<?php echo $plotCounter?>'>
                       
                    </div>
                <?php
                $plotCounter++;
            
            }
        ?>
    </div>





    























    








    <!-- AGGR-->
    



    
    






    











    <style>
        .executionResults-heading
        {
        width: 100%;
        font-size: 40px;
        color: white;
        padding: 10px;
        }
        .executionResults-graphs
        {
            width:100%;
            height: auto;
            display: flex;
            flex-wrap: wrap;
            align-content: flex-start;
        }
        .exec-eachPlot
        {
            width: 48%;
            max-height: auto;
            -webkit-box-shadow: -2px -2px 20px -10px rgba(0,0,0,0.75);
            -moz-box-shadow: -2px -2px 20px -10px rgba(0,0,0,0.75);
            box-shadow: -2px -2px 20px -10px rgba(0,0,0,0.75);
            border-radius: 2px;
            margin-left: 1%;
            overflow-x: auto;
            
        }
        .dfWrap
        {
            width: 200%;
        }
        .dfWrap table
        {
            width: 200%;            
        }
        
        .eachRep-title
        {
            font-size: 20px;
            color: white;
            font-weight: bold;
        }
        .exec-fundamental-chart
        {
            width: 100%;
            min-height: 100px;
            background-color: #8e8e9d;
            max-height: auto;
        }
        .chart-columbs
        {
            width: 100%;
            height: 30px;
            background-color: grey;
            display: inline-flex;
            color: white;
        }
        .chart-eachColumb
        {
            width: 20%;
            height: 100%;
            border-right: .5px solid #bbbbbb;
            padding-left: 5px;
        }
        .chartBody
        {
            width: 100%;
            min-height: 200px;
            max-height: auto;
            display: inline-flex;
        }
        .chartKeys
        {
            width: 20%;
            background-color: blue;
        }
        table
        {
            width: 100%;
        }
        thead
        {
            background-color: #5c8b8b;
        }
        tbody
        {
            background-color: #95999d;
            color: white;
        }
        td
        {
            padding: 5px;
        }

        .exec-mac-plot
        {
            width: 48%;
            height: 400px;
            -webkit-box-shadow: -2px -2px 20px -10px rgba(0,0,0,0.75);
            -moz-box-shadow: -2px -2px 20px -10px rgba(0,0,0,0.75);
            box-shadow: -2px -2px 20px -10px rgba(0,0,0,0.75);
            border-radius: 2px;
            margin-left: 1%;
            overflow-x: auto;
            background-color: red;
        }
        @media only screen and (max-width: 1024px)
        {
            .exec-eachPlot
            {
                width: 100%;
                margin-left: 0%;
            }
        }
   
    </style>
<?php