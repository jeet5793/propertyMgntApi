<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 
 include_once APPPATH.'/third_party/tcpdf/tcpdf.php';
class MyCustomPDFWithWatermark extends TCPDF {
	// public $template;
     
    public function setData($template){
		$this->headerImage =  $template['headerImage'];
		$this->headerContent =  $template['headerContent'];
		$this->watermarkImage =  $template['watermarkImage'];
		$this->footerContent =  $template['footerContent'];
    }
    public function Header() {
        // Get the current page break margin
        $bMargin = $this->getBreakMargin();

        // Get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;

        // Disable auto-page-break
        $this->SetAutoPageBreak(false, 0);

        // Define the path to the image that you want to use as watermark.
		if($this->headerImage!='')
		{
			 $header_logo = './'.$this->headerImage;
			 
		}else{
			 $header_logo = './assets/logo/Assetswatch.png';
		}
		if($this->watermarkImage!='')
		{
			$img_file = './'.$this->watermarkImage;
		}else{
			 $img_file = './assets/logo/water-mark.png';
		}
       
		// echo $header_logo."<br>";
		// echo $img_file."<br>";
		 // exit();
        // Render the image
		$this->Image($header_logo, 135,5, 55, 18, '', '', '', false, 300, '', false, false, 0);
        $this->Image($img_file, 53,100, '100px', '100px', '', '', '', false, 300, '', false, false, 0);
		
		if($this->headerContent!='')
		{
			$this->Cell(0, 10,$this->headerContent , 0, false, 'L', 0, '', 0, false, 'T', 'M');
		}else{
			$this->Cell(0, 10,'' , 0, false, 'C', 0, '', 0, false, 'T', 'M');
		}
        // Restore the auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);

        // Set the starting point for the page content
        $this->setPageMark();
    }
	
	/* //Page header
    public function Header() {
        // Logo
        $image_file = K_PATH_IMAGES.'logo_example.jpg';
        $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }*/

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number//'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages()
		if($this->footerContent!='')
		{
			$this->Cell(0, 10,$this->footerContent , 0, false, 'C', 0, '', 0, false, 'T', 'M');
		}else{
			$this->Cell(0, 10,'500 N Denton Tap Rd  Coppell, TX 75019 Ph: (214) 702-9959 Email: info@assetswatch.com' , 0, false, 'C', 0, '', 0, false, 'T', 'M');
		}
        
		$this->Cell(0,0,'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages() , 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}