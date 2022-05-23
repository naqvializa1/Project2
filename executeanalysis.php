
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
$jsonFundamental = json_decode($outputFundamental, TRUE);
$reportTitles = $jsonFundamental["ReportTitles"];
$reportData = $jsonFundamental["reports"];
$plots = $jsonFundamental["plots"];
$fundDfs = $jsonFundamental["dataFrame"];







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
$jsonMacro = json_decode($outputMacro, TRUE);
$reportTitlesMacro = $jsonMacro["ReportTitles"];
$reportDataMaco = $jsonMacro["reports"];
$plotsMacro = $jsonMacro["plots"];
$macroDfs = $jsonMacro["dataFrame"];
$macroDfOutPut = $jsonMacro["dataFrameOutPut"]; 
$macroLossAcc = $jsonMacro["modelLossAccuracy"];








//AGGREGATOR
$dataArray = array("assetClass"=>$assetClass, "csvFile"=>$csvFile, "model"=>$technicalModel, "analysisType"=>$analysisType, "macroModel"=>$macroModel, "macroHiddenLayers"=>$macroHiddenLayers, "macroNodesPerLayer"=>$macroNodesPerLayer);
$dataArray = json_encode($dataArray);
$outputAggro=shell_exec("python py/aggregator.py "  . base64_encode(json_encode($dataArray)));
//OUTPUTS  
$jsonAggro = json_decode($outputAggro, TRUE);
$reportTitlesAggro = $jsonAggro["ReportTitles"];
$reportDataAggro = $jsonAggro["reports"];
$plotsAggro = $jsonAggro["plots"];
$AggroDfs = $jsonAggro["dataFrame"];






?>


    <!-- FUNDAMENTAL-->
    <!-- DATAFRAMES-->
    <div class='executionResults-heading'>
        Dataframes - Fundamental
    </div>
    <div class='executionResults-graphs'>
        
    <?php
            
            foreach($fundDfs as $fundDf)
            {
                
                ?>
                    <div class='exec-eachPlot' id='exec-fundamental-eachDf<?php echo $funPlotCounter?>'>
                        
                        <div class='dfWrap'>

                            <?php echo $fundDf?>

                        </div>
                        
                    
                    </div>
                <?php
                
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

                            <?php echo $tdf?>

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
            foreach($plotsTech as $p)
            {
                $html=file_get_contents("graphs/".$p);
                ?>
                    <div class='exec-eachPlot' id='exec-eachPlot-tech<?php echo $plotTechCounter?>'>
                        <?php echo $html?>
                    </div>
                <?php
                $plotTechCounter++;
            }
        ?>
    </div>




    <!-- REPORTS-->
    <div class='executionResults-heading'>
        Reports - technical
    </div>
    <div class='executionResults-graphs'>
        <?php
            $techReportCounter = 0;
            foreach($reportTitlesTech as $trt)
            {
                
                ?>
                    <div class='exec-eachPlot' id='exec-tech-eachRep<?php echo $techReportCounter?>'>
                        <span class='eachRep-title'><?php echo $trt?></span>

                        <?php echo $reportDataTech[$techReportCounter]?>
                    
                    </div>
                <?php
                $techReportCounter++;
            }
            
        ?>


    </div>
   



    
  





   <!-- MACRO-->
    <!-- DATAFRAMES-->
    <div class='executionResults-heading'>
        Dataframes - Macro
    </div>
    <div class='executionResults-graphs'>
        <?php
            $macroDfCounter = 0;
            foreach($macroDfs as $mdf)
            {
            
                ?>
                    <div class='exec-mac-plot' id='exec-eachPlot-mac<?php echo $macroDfCounter?>'>
                     

                        <?php echo $mdf?>

                      
                    </div>
                <?php
                $macroDfCounter++;
            }
        ?>  

    </div>


    <!-- OUTPUT-->
    <div class='executionResults-heading'>
        Neural Output - Macro
    </div>
    <div class='executionResults-graphs'>

        <div class='macro-out-df'>

            <?php echo $macroDfOutPut[0]?>

        </div>
        <div class='macro-out-df'>

            <?php echo $macroDfOutPut[1]?>

        </div>
        <div class='macro-out-details'>
            <div class='executionResults-heading' style='font-size: 25px; height: auto;'>
                Model Loss & Accuracy<br>
                <?php echo $macroLossAcc?>
                
            </div>
        </div>
            

            
    </div>














    <!-- AGGR-->
    <!-- DATAFRAMES-->
    <div class='executionResults-heading'>
        Dataframes - Aggregate
    </div>
    <div class='executionResults-graphs'>
        
        <?php
            $dfCounterAgg = 0;
            foreach($AggroDfs as $agdf)
            {
                
                ?>
                    <div class='exec-mac-plot' id='exec-tech-eachDf<?php echo $dfCounterAgg?>'>
                        
                      

                        <?php echo $agdf?>

                     
                        
                    
                    </div>
                <?php
                $dfCounterAgg++;
            }
            
        ?>


    </div>



     <!-- REPORTS-->
     <div class='executionResults-heading'>
        Reports - Aggregate
    </div>
    <div class='executionResults-graphs'>
        <?php
            $reportCounterAAgg = 0;
            foreach($reportTitlesAggro as $titleAgg)
            {
                
                ?>
                    <div class='exec-eachPlot' id='exec-fundamental-eachRep<?php echo $reportCounterAAgg?>'>
                        <span class='eachRep-title'><?php echo $titleAgg?></span>

                        <?php echo $reportDataAggro[$reportCounterAAgg]?>
                    
                    </div>
                <?php
                $reportCounterAAgg++;
            }
            
        ?>


    </div>



    
    






    











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
            height: 300px;
            -webkit-box-shadow: -2px -2px 20px -10px rgba(0,0,0,0.75);
            -moz-box-shadow: -2px -2px 20px -10px rgba(0,0,0,0.75);
            box-shadow: -2px -2px 20px -10px rgba(0,0,0,0.75);
            border-radius: 2px;
            margin-left: 1%;
            overflow-x: auto;
            overflow-y: auto;
            position: relative;
        }
        .exec-mac-plot table
        {
            position: absolute;
            width: 1500%;      

        }
       .macro-out-df
       {
            width: 48%;
            height: 250px;
            -webkit-box-shadow: -2px -2px 20px -10px rgba(0,0,0,0.75);
            -moz-box-shadow: -2px -2px 20px -10px rgba(0,0,0,0.75);
            box-shadow: -2px -2px 20px -10px rgba(0,0,0,0.75);
            border-radius: 2px;
            margin-left: 1%;
            overflow-x: auto;
            overflow-y: auto;
            position: relative;
       }
       .macro-out-details
       {
            width: 48%;
            height: 250px;
            -webkit-box-shadow: -2px -2px 20px -10px rgba(0,0,0,0.75);
            -moz-box-shadow: -2px -2px 20px -10px rgba(0,0,0,0.75);
            box-shadow: -2px -2px 20px -10px rgba(0,0,0,0.75);
            border-radius: 2px;
            margin-left: 1%;
            overflow-x: auto;
            overflow-y: auto;
            position: relative;
       }
        @media only screen and (max-width: 1024px)
        {
            .exec-mac-plot
            {
                width: 100%;
                margin-left: 0%;
            }
            .exec-eachPlot
            {
                width: 100%;
                margin-left: 0%;
            }
            .macro-out-df
            {
                width: 100%;
                margin-left: 0%;
            }
            .macro-out-details
            {
                width: 100%;
                margin-left: 0%;
            }
        }
   
    </style>
<?php