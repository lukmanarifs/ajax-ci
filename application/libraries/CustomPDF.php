<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH . 'libraries/FPDF.php');

class CustomPDF extends FPDF {
    private $ci;


    function header(){
      $this->ci =& get_instance();
      $this->ci->load->model('kantor_model');
      //Move to the right
  		//$this->Cell(5);
  		//Title
      $data = $this->ci->kantor_model->cari();

      if(!$data){
		    $this->SetFont('Arial','B',13);
        $this->Cell(60,5,"",0,1);
    		//$this->Cell(5);
    		$this->SetFont('Arial','',8);
    		$this->Cell(60,3,"",0,1);
      }else{
        $this->Image(base_url().'/public/images/pdf/'.$data->kantor_logo,15,8,$data->kantor_lebar_logo,20);
        $this->Cell(14);
        $this->SetFont('Arial','B',12);
        $this->Cell(160,6,'PEMERINTAH KABUPATEN SUMENEP',0,1, 'C');
        $this->Cell(14);
        $this->Cell(160,6,$data->kantor_nama,0,1, 'C');
    		//$this->Cell(5);
    		$this->SetFont('Arial','',8);
        $this->Cell(14);
    		$this->Cell(160,4,$data->kantor_alamat.", Telp :".$data->kantor_telepon.", fax :".$data->kantor_fax.", email :".$data->kantor_email,0,1, 'C');
        $this->SetFont('Arial','B',10);
        $this->Cell(14);
        $this->Cell(160,5,'SUMENEP',0,1, 'C');

      }
      $this->Line(15, 32, 210-15, 32);
  		$this->Ln(8);

    }
    ///////////////////////////////////////
    var $widths;
    var $aligns;

    function SetWidths($w)
    {
    	//Set the array of column widths
    	$this->widths=$w;
    }

    function SetAligns($a)
    {
    	//Set the array of column alignments
    	$this->aligns=$a;
    }

    function Row($data)
    {
    	//Calculate the height of the row
    	$nb=1;
    	for($i=0;$i<count($data);$i++)
    		$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    	$h=5*$nb;
    	//Issue a page break first if needed
    	$this->CheckPageBreak($h);
    	//Draw the cells of the row
    	for($i=0;$i<count($data);$i++)
    	{
    		$w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $this->SetFont('Arial','',9);

    		//Save the current position
    		$x=$this->GetX();
    		$y=$this->GetY();
    		//Draw the border
    		$this->Rect($x,$y,$w,$h,1);
    		//Print the text
    		$this->MultiCell($w,5,$data[$i],0,$a);
    		//Put the position to the right of the cell
    		$this->SetXY($x+$w,$y);
    	}
    	//Go to the next line
    	$this->Ln($h);
    }

    function CheckPageBreak($h)
    {
    	//If the height h would cause an overflow, add a new page immediately
    	if($this->GetY()+$h>$this->PageBreakTrigger)
    		$this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt)
    {
    	//Computes the number of lines a MultiCell of width w will take
    	$cw=&$this->CurrentFont['cw'];
    	if($w==0)
    		$w=$this->w-$this->rMargin-$this->x;
    	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    	$s=str_replace("\r",'',$txt);
    	$nb=strlen($s);
    	if($nb>0 and $s[$nb-1]=="\n")
    		$nb--;
    	$sep=-1;
    	$i=0;
    	$j=0;
    	$l=0;
    	$nl=1;
    	while($i<$nb)
    	{
    		$c=$s[$i];
    		if($c=="\n")
    		{
    			$i++;
    			$sep=-1;
    			$j=$i;
    			$l=0;
    			$nl++;
    			continue;
    		}
    		if($c==' ')
    			$sep=$i;
    		$l+=$cw[$c];
    		if($l>$wmax)
    		{
    			if($sep==-1)
    			{
    				if($i==$j)
    					$i++;
    			}
    			else
    				$i=$sep+1;
    			$sep=-1;
    			$j=$i;
    			$l=0;
    			$nl++;
    		}
    		else
    			$i++;
    	}
    	return $nl;
    }
    //Page footer
  	function footer()
  	{
  		//Position at 15 mm from bottom
  		$this->SetY(-25);
  		//Arial italic 8
  		$this->SetFont('Arial','',8);
  		//Page number
  		$this->Cell(85,5,'Jam / Tanggal Cetak : '.date('H.i.s / d.m.Y').'','T',0,'L');
  		$this->Cell(88,5,trim('Halaman : '.$this->PageNo().' / {nb}'),'T',0,'R');
  	}
    public function getInstance(){
        return new CustomPDF('p','mm','A4');
    }
}
?>
