<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH . 'libraries/PDFSector.php');
class PDFDiag extends PDFSector {
		var $legends;
		var $wLegend;
		var $sum;
		var $NbVal;
		private $ci;

    function header(){
      $this->ci =& get_instance();
      $this->ci->load->model('kantor_model');
      $data = $this->ci->kantor_model->cari();

      if(!$data){
		    $this->SetFont('Arial','B',13);
        $this->Cell(60,5,"",0,1);
    		//$this->Cell(5);
    		$this->SetFont('Arial','',8);
    		$this->Cell(60,3,"",0,1);
      }else{
        $this->Image(base_url().'/public/images/pdf/'.$data->kantor_logo,15,8,$data->kantor_lebar_logo,15);
        $this->Cell(14);
        $this->SetFont('Arial','B',12);
        $this->Cell(60,5,$data->kantor_nama,0,1);
    		//$this->Cell(5);
    		$this->SetFont('Arial','',7);
        $this->Cell(14);
    		$this->Cell(60,3,$data->kantor_alamat,0,1);
      }

      $this->Line(15, 22, 210-15, 22);
  		$this->Ln(8);

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
		function PieChart($w, $h, $data, $format, $colors=null)
		{
				$this->SetFont('Courier', '', 9);
				$this->SetLegends($data,$format);
				$XPage = $this->GetX();
				$YPage = $this->GetY();
				$margin = 2;
				$hLegend = 5;
				$radius = min($w - $margin * 4 - $hLegend - $this->wLegend, $h - $margin * 2);
				$radius = floor($radius / 2);
				$XDiag = $XPage + $margin + $radius;
				$YDiag = $YPage + $margin + $radius;
				if($colors == null) {
						for($i = 0; $i < $this->NbVal; $i++) {
								$gray = ($i+1) * intval(255 / $this->NbVal);
								$gray1 = ($i+1) * intval(175 / $this->NbVal);
								$gray2 = ($i+1) * intval(100 / $this->NbVal);
								$colors[$i] = array($gray,$gray1,$gray2);
						}
				}
				//Sectors
				$this->SetLineWidth(0.2);
				$angleStart = 0;
				$angleEnd = 0;
				$i = 0;
				foreach($data as $val) {
						$angle = ($val * 360) / doubleval($this->sum);
						if ($angle != 0) {
								$angleEnd = $angleStart + $angle;
								$this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
								$this->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
								$angleStart += $angle;
						}
						$i++;
				}
				//Legends
				$this->SetFont('Courier', '', 9);
				$x1 = $XPage + 2 * $radius + 4 * $margin;
				$x2 = $x1 + $hLegend + $margin;
				$y1 = $YDiag - $radius + (2 * $radius - $this->NbVal*($hLegend + $margin)) / 2;
				for($i=0; $i<$this->NbVal; $i++) {
						$this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
						$this->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
						$this->SetXY($x2,$y1);
						$this->Cell(0,$hLegend,$this->legends[$i]);
						$y1+=$hLegend + $margin;
				}
		}
		function BarDiagram($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4)
		{
				$this->SetFont('Courier', '', 9);
				$this->SetLegends($data,$format);
				$XPage = $this->GetX();
				$YPage = $this->GetY();
				$margin = 2;
				$YDiag = $YPage + $margin;
				$hDiag = floor($h - $margin * 2);
				$XDiag = $XPage + $margin * 2 + $this->wLegend;
				$lDiag = floor($w - $margin * 3 - $this->wLegend);
				if($color == null)
						$color=array(155,155,155);
				if ($maxVal == 0) {
						$maxVal = max($data);
				}
				$valIndRepere = ceil($maxVal / $nbDiv);
				$maxVal = $valIndRepere * $nbDiv;
				$lRepere = floor($lDiag / $nbDiv);
				$lDiag = $lRepere * $nbDiv;
				$unit = $lDiag / $maxVal;
				$hBar = floor($hDiag / ($this->NbVal + 1));
				$hDiag = $hBar * ($this->NbVal + 1);
				$eBaton = floor($hBar * 80 / 100);
				$this->SetLineWidth(0.2);
				$this->Rect($XDiag, $YDiag, $lDiag, $hDiag);
				$this->SetFont('Courier', '', 9);
				$this->SetFillColor($color[0],$color[1],$color[2]);
				$i=0;
				foreach($data as $val) {
						//Bar
						$xval = $XDiag;
						$lval = (int)($val * $unit);
						$yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
						$hval = $eBaton;
						$this->Rect($xval, $yval, $lval, $hval, 'DF');
						//Legend
						$this->SetXY(0, $yval);
						$this->Cell($xval - $margin, $hval, $this->legends[$i],0,0,'R');
						$i++;
				}
				//Scales
				for ($i = 0; $i <= $nbDiv; $i++) {
						$xpos = $XDiag + $lRepere * $i;
						$this->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
						$val = $i * $valIndRepere;
						$xpos = $XDiag + $lRepere * $i - $this->GetStringWidth($val) / 2;
						$ypos = $YDiag + $hDiag - $margin;
						$this->Text($xpos, $ypos, $val);
				}
		}
		function SetLegends($data, $format)
		{
				$this->legends=array();
				$this->wLegend=0;
				$this->sum=array_sum($data);
				$this->NbVal=count($data);
				foreach($data as $l=>$val)
				{
						$p=sprintf('%.2f',$val/$this->sum*100).'%';
						$legend=str_replace(array('%l','%v','%p'),array($l,number_format($val,0,"", "."),$p),$format);
						$this->legends[]=$legend;
						$this->wLegend=max($this->GetStringWidth($legend),$this->wLegend);
				}
		}
		// public function getInstance(){
		// 		return new PDFDiag('p','mm','A4');
		// }
}
?>
