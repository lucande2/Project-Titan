<?php
/*
    engine/proccesses/analysis_chart.php    VERSION 1.3
    Script which determines colours in pie ring charts on analysis centre screens.
    Reviewed 7/12/2023
*/

include_once '../../engine/dbConnect.php';

function generateAnalysisColor($analysisResult) {
    switch ($analysisResult) {
        case "good":
            return "#00b528";
        case "low":
            return "#ffd104";
        case "high":
            return "#ff0000";
        default:
            return "#000000"; // Default color in case an unexpected value is received.
    }
}
