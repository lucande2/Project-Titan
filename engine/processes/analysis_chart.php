<?php

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
