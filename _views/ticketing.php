<?php
/**
 * Electronic ticket (PDF) with a QR code.
 * The QR code leads to /wipe{iden}: the operator scans it at the entrance.
 */

define('FPDF_FONTPATH', ROOT . '/template/lib/pdf/font/');
require_once ROOT . '/template/lib/pdf/fpdf.php';
require_once ROOT . '/template/lib/pdf/qr-code/qrcode.class.php';

$bean    = G::var('bean');
$iden    = G::var('iden');
$login   = G::var('login');
$profile = G::var('profile');
$used    = (int) $bean->status === Booking::USED;

// FPDF works with single-byte fonts: the bundled Arial uses cp1251
$t = function ($text) {
	$text = (string) $text;
	if (function_exists('iconv'))
	{
		$converted = @iconv('UTF-8', 'windows-1251//TRANSLIT', $text);
		if ($converted !== false)
		{
			return $converted;
		}
	}
	return function_exists('mb_convert_encoding') ? mb_convert_encoding($text, 'Windows-1251', 'UTF-8') : $text;
};

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$checkUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/wipe' . $iden;
$viewer = $profile ? trim($profile->surname . ' ' . $profile->name . ' ' . $profile->patronymic) : '';

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->SetAuthor('Cinema Night');
$pdf->SetTitle($t('Билет ' . $iden), false);
$pdf->SetAutoPageBreak(false);
$pdf->AddFont('Arial', '', 'arial.php');
$pdf->AddPage();

// ---------------------------------------------------------------- card
$x = 20; $y = 20; $w = 170; $h = 120;

$pdf->SetFillColor(44, 44, 44);
$pdf->Rect($x, $y, $w, 28, 'F');

$logo = ROOT . '/template/image/logoCN1.png';
if (is_file($logo))
{
	$pdf->Image($logo, $x + 6, $y + 4, 20, 20);
}

$pdf->SetTextColor(243, 188, 70);
$pdf->SetFont('Arial', '', 20);
$pdf->SetXY($x + 30, $y + 6);
$pdf->Cell(100, 9, 'CINEMA NIGHT', 0, 2);
$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(220, 220, 220);
$pdf->Cell(100, 6, $t('Киноночь · Алматы'), 0, 0);

$pdf->SetXY($x + $w - 66, $y + 8);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 5, $t('ЭЛЕКТРОННЫЙ БИЛЕТ'), 0, 2, 'R');
$pdf->SetFont('Arial', '', 16);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(60, 9, $t('№ ' . $iden), 0, 0, 'R');

// body
$pdf->SetDrawColor(220, 220, 220);
$pdf->SetFillColor(250, 250, 250);
$pdf->Rect($x, $y + 28, $w, $h - 28, 'DF');

// QR code
$qr = new QrCode($checkUrl, 'M');
$qr->disableBorder();
$qr->displayFPDF($pdf, $x + $w - 66, $y + 36, 58);

$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(130, 130, 130);
$pdf->SetXY($x + $w - 66, $y + 96);
$pdf->MultiCell(58, 4, $t('Покажите QR-код на входе'), 0, 'C');

// details
$row = function ($label, $value) use ($pdf, $t, $x) {
	$pdf->SetX($x + 8);
	$pdf->SetFont('Arial', '', 9);
	$pdf->SetTextColor(140, 140, 140);
	$pdf->Cell(30, 7, $t($label), 0, 0);
	$pdf->SetFont('Arial', '', 12);
	$pdf->SetTextColor(40, 40, 40);
	$pdf->MultiCell(56, 7, $t($value), 0, 'L');
};

$pdf->SetXY($x + 8, $y + 36);
$row('Зритель', $viewer !== '' ? $viewer : $login);
$row('Логин', $login);
$row('Код', $bean->code);
$row('Цена', number_format($bean->price, 0, '', ' ') . ' тг (' . number_format($bean->cost, 0, '', ' ') . ' тг/место)');
$row('Бронь от', date('d.m.Y H:i', strtotime($bean->date)));

// seats
$seatY = $pdf->GetY() + 3;
$pdf->SetXY($x + 8, $seatY);
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(140, 140, 140);
$pdf->Cell(30, 7, $t('Места'), 0, 0);

$chipX = $x + 38;
$chipY = $seatY;
$pdf->SetFont('Arial', '', 10);
$pdf->SetFillColor(255, 195, 66);
$pdf->SetTextColor(40, 40, 40);

foreach (Booking::seats($bean) as $seat)
{
	$s = Hall::parse($seat);
	$label = $t('Ряд ' . $s['row'] . ' · Место ' . $s['seat']);
	$chipW = $pdf->GetStringWidth($label) + 6;

	if ($chipX + $chipW > $x + $w - 70)
	{
		$chipX = $x + 38;
		$chipY += 8;
	}

	$pdf->SetXY($chipX, $chipY);
	$pdf->Cell($chipW, 6, $label, 0, 0, 'C', true);
	$chipX += $chipW + 3;
}

// dashed tear line
$pdf->SetDrawColor(200, 200, 200);
for ($dx = $x; $dx < $x + $w; $dx += 4)
{
	$pdf->Line($dx, $y + $h + 6, $dx + 2, $y + $h + 6);
}

// rules
$pdf->SetXY($x, $y + $h + 12);
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(110, 110, 110);
$pdf->MultiCell($w, 5, $t(
	"Билет действителен для одного прохода на все фильмы сеанса.\n" .
	"QR-код погашается на входе: повторно пройти по нему нельзя.\n" .
	"Проверка билета: " . $checkUrl
), 0, 'L');

// "used" stamp
if ($used)
{
	// covers the QR code: it can't be used again
	$pdf->SetTextColor(230, 47, 47);
	$pdf->SetDrawColor(230, 47, 47);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetLineWidth(1);
	$pdf->Rect($x + $w - 68, $y + 56, 62, 18, 'DF');
	$pdf->SetFont('Arial', '', 18);
	$pdf->SetXY($x + $w - 68, $y + 56);
	$pdf->Cell(62, 18, $t('ИСПОЛЬЗОВАН'), 0, 0, 'C');
}

$pdf->Output('I', 'cinema-night_' . $iden . '.pdf');
