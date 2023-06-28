<?php
namespace App\Classes\reports;


require('fpdf.php');
// require('sector.php');
session_start();
date_default_timezone_set('Africa/Nairobi');

class PDF extends FPDF
{
    public $company_logo = "/ladybirdwhite.png";
    protected $school_name = "Demo School";
    public $school_contact = "0743551250";
    public $school_document_title = "Exam Paper";
    public $website_name = "ladybirdsmis.com";
    public $box = "153";
    public $code = "50400";
    protected $school_header_position = 200;

    // set company_logo
    function setCompayLogo($logo)
    {
        $this->company_logo = $logo;
    }
    // set website name
    // set company_logo
    function setWebsiteName($web_name)
    {
        $this->website_name = $web_name;
    }
    // set school_name
    function set_school_name($sch_name)
    {
        $this->school_name = $sch_name;
    }
    // set school_box_code
    function set_school_contact($sch_contacts)
    {
        $this->school_contact = $sch_contacts;
    }
    // set school_box_code
    function set_document_title($title)
    {
        $this->school_document_title = $title;
    }
    // set school_box
    function set_document_box($box_value)
    {
        $this->box = $box_value;
    }
    // set school_code
    function set_document_code($code_value)
    {
        $this->code = $code_value;
    }
    // Load data
    function LoadData($file)
    {
        // Read file lines
        $lines = file($file);
        $data = array();
        foreach ($lines as $line)
            $data[] = explode(';', trim($line));
        return $data;
    }

    // Page header
    function Header()
    {
        $this->SetY(10);
        // Logo
        $this->Image($this->company_logo, 3, 8, 20);
        // Arial  15
        $this->SetFont('Helvetica', 'B', 15);
        // Title
        $this->Cell($this->school_header_position, 5, strtoupper($this->school_name), 0, 0, 'C');
        $this->Ln();
        $this->SetFont('Helvetica', '', 8);
        $this->Cell($this->school_header_position, 5, "Contact Us: " . $this->school_contact, 0, 0, 'C');
        $this->Ln();
        $this->Cell($this->school_header_position, 5, "P.O. Box : " . $this->box."-". $this->code, 0, 0, 'C');
        $this->SetFont('Helvetica', 'B', 10);
        $this->Ln();
        $this->Cell($this->school_header_position, 5,
        /** "Report Title: " . **/
        $this->school_document_title . "", 0, 0, 'C');
        $this->SetTitle($this->school_document_title);
        $this->SetFont('', '');
        $this->SetAuthor(session('Usernames'));
        // Line break
        if ($this->school_header_position == 200) {
            // potrait
            $this->Ln(10);
            $this->Cell(190, 0, "", 1);
        }
        $this->Ln();
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 7);
        // Page number
        $this->Cell(0, 5, 'Page ' . $this->PageNo() . '', 0, 0, 'C');
        $this->Ln();
        $this->SetFont('Arial', 'I', 7);
        $this->Cell($this->school_header_position, 7, "If found please return to " . ucwords(strtolower(trim($this->school_name))) . " or contact " . $this->school_contact . "",0,0,'C');
    }

    function setHeaderPos($pos)
    {
        $this->school_header_position = $pos;
    }
    function isJson_report_fin($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }

    // Colored table
    function financeTable($header, $data, $width)
    {
        // Colors, line width and bold font
        $this->SetFillColor(157, 183, 184);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'L', true);
        }

        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(205, 211, 218);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 8);
        // Data
        $fill = false;
        $recieved = 0;
        $balance = 0;
        foreach ($data as $row) {
            // for ($$index=0; $$index < count($row); $$index++) { 
            //     $this->Cell($w[$index], 6, $row[$index], 1, 0, 'L', $fill);
            // }
            $this->Cell($w[0], 6, $row[0], 1, 0, 'L', $fill);
            $this->Cell($w[1], 6, "Kes " . number_format($row[1]), 1, 0, 'L', $fill);
            $this->Cell($w[2], 6, $row[2], 1, 0, 'L', $fill);
            $this->Cell($w[3], 6, ($row[3]), 1, 0, 'C', $fill);
            $this->Cell($w[4], 6, $row[4], 1, 0, 'L', $fill);
            $this->Cell($w[5], 6, date("D dS M Y",strtotime($row[5]))." ".$row[6], 1, 0, 'L', $fill);
            $this->Cell($w[6], 6, ($this->isJson_report_fin($row[7]) ? count(json_decode($row[7])) : "0") . " Document(s)", 1, 0, 'L', $fill);
            $this->Ln();
            $fill = !$fill;
            $recieved += $row[1];
        }
        $this->SetFont('Helvetica', 'BI', 8);
        $this->Cell($w[0], 6, "Tot", 1, 0, 'L', $fill);
        $this->Cell($w[1], 6, "Kes " . number_format($recieved), 1, 0, 'L', $fill);
        // $this->Cell($w[2], 6, "Kes " . number_format($balance), 1, 0, 'L', $fill);
        // Closing line
        // $this->Cell(array_sum($w), 0, '', 'T');
    }
    // Colored table
    function FancyTable($header, $data, $width)
    {
        // Colors, line width and bold fontrgb(82, 170, 216)
        $this->SetFillColor(82, 170, 216);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restorationrgb(204, 230, 244)
        $this->SetFillColor(204, 230, 244);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 6);
        // Data
        $fill = false;
        $counter = 1;
        foreach ($data as $row) {
            $this->Cell($w[0], 5, $counter, 1, 0, 'L', $fill);
            $this->Cell($w[1], 5, ucwords(strtolower($row[0])), 1, 0, 'L', $fill);
            $this->Cell($w[2], 5, strtoupper($row[1]), 1, 0, 'L', $fill);
            $this->SetFont('Helvetica', '', 4);
            $this->Cell($w[3], 5, date("dS M Y @ H:i:s",strtotime($row[2])), 1, 0, 'C', $fill);
            $this->Cell($w[4], 5, date("dS M Y @ H:i:s",strtotime($row[3])), 1, 0, 'C', $fill);
            $this->SetFont('Helvetica', '', 6);
            $this->Cell($w[5], 5, "Kes ".number_format($row[8]), 1, 0, 'L', $fill);
            $this->Cell($w[6], 5, ($row[5]), 1, 0, 'L', $fill);
            $this->Cell($w[7], 5, ($row[6]), 1, 0, 'L', $fill);
            $this->Cell($w[8], 5, ($row[7]), 1, 0, 'L', $fill);
            $this->Cell($w[9], 5, ucwords(strtolower($row[9])), 1, 0, 'L', $fill);
            $this->Ln();
            $fill = !$fill;
            $counter++;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    function clientInformation($header, $data, $width)
    {
        // Colors, line width and bold fontrgb(82, 170, 216)
        $this->SetFillColor(82, 170, 216);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restorationrgb(204, 230, 244)
        $this->SetFillColor(204, 230, 244);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 6);
        // Data
        $fill = false;
        $counter = 1;
        foreach ($data as $row) {
            // rgb(255, 199, 199)rgb(170, 223, 170)
            if ($row[10] == "0") {
                $this->SetFillColor(255, 199, 199);
            }else{
                $this->SetFillColor(170, 223, 170);
            }
            $this->Cell($w[0], 5, $counter, 1, 0, 'L', true);
            $this->SetFillColor(204, 230, 244);
            $this->Cell($w[1], 5, ucwords(strtolower($row[0])), 1, 0, 'L', $fill);
            $this->Cell($w[2], 5, strtoupper($row[1]), 1, 0, 'L', $fill);
            $this->SetFont('Helvetica', '', 4);
            $this->Cell($w[3], 5, date("dS M Y @ H:i:s",strtotime($row[2])), 1, 0, 'C', $fill);
            $this->Cell($w[4], 5, date("dS M Y @ H:i:s",strtotime($row[3])), 1, 0, 'C', $fill);
            $this->SetFont('Helvetica', '', 6);
            $this->Cell($w[5], 5, "Kes ".number_format($row[3]), 1, 0, 'L', $fill);
            $this->Cell($w[6], 5, ($row[2]), 1, 0, 'L', $fill);
            // fill color for static and pppoe assigned
            if ($row[13] == "static") {//rgb(201, 186, 181)rgb(204, 199, 228)
                $this->SetFillColor(201, 186, 181);
            }else{
                $this->SetFillColor(204, 199, 228);
            }
            $this->Cell($w[7], 5, ($row[13]), 1, 0, 'L', true);
            $this->SetFillColor(204, 230, 244);
            $this->Cell($w[8], 5, ($row[5]), 1, 0, 'L', $fill);
            $this->Cell($w[9], 5, ucwords(strtolower($row[8])), 1, 0, 'L', $fill);
            $this->Cell($w[10], 5, ucwords(strtolower($row[9])), 1, 0, 'L', $fill);
            if ($row[11] == "In-Active") {
                $this->Cell($w[11], 5, ucwords(strtolower($row[11])), 1, 0, 'L', $fill);
            }else{
                $this->SetFont('Helvetica', '', 4);
                $this->Cell($w[11], 5, ucwords(strtolower($row[11])), 1, 0, 'L', $fill);
                $this->SetFont('Helvetica', '', 6);
            }
            $this->Ln();
            $fill = !$fill;
            $counter++;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    function clientRouterInformation($header, $data, $width)
    {
        // Colors, line width and bold fontrgb(82, 170, 216)
        $this->SetFillColor(82, 170, 216);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restorationrgb(204, 230, 244)
        $this->SetFillColor(204, 230, 244);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 6);
        // Data
        $fill = false;
        $counter = 1;
        foreach ($data as $row) {
            // rgb(255, 199, 199)rgb(170, 223, 170)
            if ($row[10] == "0") {
                $this->SetFillColor(255, 199, 199);
            }else{
                $this->SetFillColor(170, 223, 170);
            }
            $this->Cell($w[0], 5, $counter, 1, 0, 'L', true);
            $this->SetFillColor(204, 230, 244);
            $this->Cell($w[1], 5, ucwords(strtolower($row[0])), 1, 0, 'L', $fill);
            $this->Cell($w[2], 5, strtoupper($row[1]), 1, 0, 'L', $fill);
            $this->SetFont('Helvetica', '', 4);
            $this->Cell($w[3], 5, date("dS M Y @ H:i:s",strtotime($row[6])), 1, 0, 'C', $fill);
            $this->Cell($w[4], 5, date("dS M Y @ H:i:s",strtotime($row[7])), 1, 0, 'C', $fill);
            $this->SetFont('Helvetica', '', 6);
            $this->Cell($w[5], 5, ($row[3]), 1, 0, 'L', $fill);
            $this->Cell($w[6], 5, ($row[2]), 1, 0, 'L', $fill);
            // fill color for static and pppoe assigned
            if ($row[13] == "static") {//rgb(201, 186, 181)rgb(204, 199, 228)
                $this->SetFillColor(201, 186, 181);
            }else{
                $this->SetFillColor(204, 199, 228);
            }
            $this->Cell($w[7], 5, ($row[13]), 1, 0, 'L', true);
            $this->SetFillColor(204, 230, 244);
            $this->Cell($w[8], 5, ($row[5]), 1, 0, 'L', $fill);
            $this->Cell($w[9], 5, ucwords(strtolower($row[8])), 1, 0, 'L', $fill);
            $this->Cell($w[10], 5, ucwords(strtolower($row[9])), 1, 0, 'L', $fill);
            $this->Cell($w[11], 5, ucwords(strtolower($row[11])), 1, 0, 'L', $fill);
            $this->Ln();
            $fill = !$fill;
            $counter++;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    function transactionReports($header,$data,$width){
        // Colors, line width and bold fontrgb(82, 170, 216)
        $this->SetFillColor(82, 170, 216);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restorationrgb(204, 230, 244)
        $this->SetFillColor(204, 230, 244);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 6);
        // Data
        $fill = false;
        $counter = 1;
        foreach ($data as $row) {
            $this->Cell($w[0], 5, $counter, 1, 0, 'L', $fill);
            $this->Cell($w[1], 5, $row[0], 1, 0, 'L', $fill);
            $this->Cell($w[2], 5, $row[1], 1, 0, 'L', $fill);
            $this->Cell($w[3], 5, $row[2], 1, 0, 'L', $fill);
            $this->Cell($w[4], 5, "Kes ".number_format($row[3]), 1, 0, 'L', $fill);
            $this->Cell($w[5], 5, date("D dS M Y @ h:i:s A",strtotime($row[4])), 1, 0, 'L', $fill);
            $this->Cell($w[6], 5, $row[5], 1, 0, 'L', $fill);
            $this->Cell($w[7], 5, $row[6], 1, 0, 'L', $fill);
            $this->Ln();
            $fill = !$fill;
            $counter++;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    function smsTable($header,$data,$width){
        // Colors, line width and bold fontrgb(82, 170, 216)
        $this->SetFillColor(82, 170, 216);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restorationrgb(204, 230, 244)
        $this->SetFillColor(204, 230, 244);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 6);
        // Data
        $fill = false;
        $counter = 1;
        foreach ($data as $row) {
            $this->Cell($w[0], 5, $row[0], 1, 0,'J', $fill);
            $this->Cell($w[1], 5, $row[1], 1, 0,'J', $fill);
            $this->Cell($w[2], 5, date("dS M Y @ H:i:s",strtotime($row[2])), 1, 0,'J', $fill);
            $this->Cell($w[3], 5, $row[3], 1, 0,'J', $fill);
            $this->Ln();
            $fill = !$fill;
            $counter++;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    function ExpenseTable($header, $data, $width)
    {
        // Colors, line width and bold fontrgb(82, 170, 216)
        $this->SetFillColor(82, 170, 216);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restorationrgb(204, 230, 244)
        $this->SetFillColor(204, 230, 244);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 6);
        // Data
        $fill = false;
        $counter = 1;
        foreach ($data as $row) {
            $this->Cell($w[0], 5, $counter, 1, 0, 'L', $fill);
            $this->Cell($w[1], 5, $row[0], 1, 0, 'L', $fill);
            $this->Cell($w[2], 5, $row[1], 1, 0, 'L', $fill);
            $this->Cell($w[3], 5, "Kes ".number_format(round($row[3],2)), 1, 0, 'L', $fill);
            $this->Cell($w[4], 5, $row[4]." ".($row[2] != null ? $row[2] : ""), 1, 0, 'L', $fill);
            $this->Cell($w[5], 5, "Kes ".number_format(round($row[5],2)), 1, 0, 'L', $fill);
            $this->Cell($w[6], 5, $row[6], 1, 0, 'L', $fill);
            $this->Ln();
            $fill = !$fill;
            $counter++;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}


function receiptNo($no){
    if (strlen($no) < 3) {
        if(strlen($no) == 2){
            return "0".$no;
        }else{
            return "00".$no;
        }
    }
    return $no;
}
