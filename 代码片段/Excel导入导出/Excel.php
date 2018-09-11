<?php
namespace app\core\helper;

use PHPExcel;

class ImportExcel
{
	/**
	 * @param $file
	 * @return array|bool
	 * excel导入
	 */
	public static function Excel($file) {

		$objReader = new \PHPExcel_Reader_Excel5();
		if (!$objReader->canRead($file)) {
			return false;
		}

		$objPHPExcel = $objReader->load($file);

		$objWorksheet = $objPHPExcel->getSheet(0);
		$highestRow = $objWorksheet->getHighestRow();//最大行数，为数字
		$highestColumn = $objWorksheet->getHighestColumn();//最大列数 为字母
		$highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn); //将字母变为数字

		//组装数据
		$tableData = [];
		for ($row = 2; $row <= $highestRow; $row++) {
			for ($col = 0; $col < $highestColumnIndex; $col++) {
				$tableData[$row - 1][$col] = (string)preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$objWorksheet->getCellByColumnAndRow($col, $row)->getValue());
			}
			if(implode($tableData[$row-1], '') == '') {
				unset($tableData[$row-1]);
			}
		}
		return $tableData;
	}

	/**
	 * @param $filename
	 * @param $title
	 * @param $rows
	 * excel导出
	 */
	public static function exportCsv($filename,$title,$keys,$rows) {
		$filename = $filename . '.csv'; //设置文件名
		ob_end_clean();
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		header("Content-Type:text/html;charset=utf-8");
		/*header("Content-type:application/vnd.ms-excel;charset=UTF-8");*/
		header('Content-Type:text/csv;charset=utf-8');
		header("Content-Disposition: attachment;filename={$filename}");
		$out = fopen('php://output', 'w');
		fputcsv($out, $title);
		foreach ($rows as $k=>$row) {
			$line = [];
				foreach ($keys as $value){
					if(isset($row[$value])){
						$line[] = iconv('utf-8', 'gb2312', $row[$value]);
					}
				}
			fputcsv($out, $line);
		}
		fclose($out);
	}

	/**
	 * @param $filename
	 * @param $title
	 * @param $indexKey
	 * @param $list
	 * @param int $startRow
	 * @param bool $excel2007
	 * excel导出
	 */
	public static function exportExcel($filename,$title,$indexKey,$list,$startRow=2,$excel2007=false){
		ob_end_clean();//清除缓冲区,避免乱码
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		//初始化PHPExcel()
		$objPHPExcel = new \PHPExcel();
		//设置保存版本格式
		if($excel2007){
			$objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
			$filename = $filename.'.xlsx';
		}else{
			$objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
			$filename = $filename.'.xls';
		}
		//设置表头
		//设置sheet的名称
		$objPHPExcel->getActiveSheet()->setTitle($filename);
		//设置列名
		$titlekey = 0;
		foreach($title as $v){
			//注意，不能少了。将列数字转换为字母
			$colum = \PHPExcel_Cell::stringFromColumnIndex($titlekey);
			$objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
			$titlekey += 1;
		}
		//接下来就是写数据到表格里面去
		$objActSheet = $objPHPExcel->getActiveSheet();
		foreach ($list as $row) {
			$span = 0;
			foreach ($indexKey as $key => $value){
				//注意，不能少了。将列数字转换为字母
				$j = \PHPExcel_Cell::stringFromColumnIndex($span);
				//这里是设置单元格的内容
				if(isset($row[$value])){
					$objActSheet->setCellValue($j.$startRow,$row[$value]);
				}
				$span++;
			}
			$startRow++;
		}
		// 下载这个表格，在浏览器输出
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");;
		header('Content-Disposition:attachment;filename='.$filename.'');
		header("Content-Transfer-Encoding:binary");
		$objWriter->save('php://output');//文件通过浏览器下载
	}




	/**
	 * @param $file
	 * @return array|bool
	 * yield导入
	 */
	public static function yieldExcel($file) {

		$objReader = new \PHPExcel_Reader_Excel5();
		if (!$objReader->canRead($file)) {
			yield false;
		}

		$objPHPExcel = $objReader->load($file);

		$objWorksheet = $objPHPExcel->getSheet(0);
		$highestRow = $objWorksheet->getHighestRow();//最大行数，为数字
		$highestColumn = $objWorksheet->getHighestColumn();//最大列数 为字母
		$highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn); //将字母变为数字

		//组装数据
		$tableData = [];
		for ($row = 2; $row <= $highestRow; $row++) {
			for ($col = 0; $col < $highestColumnIndex; $col++) {
				$tableData[] = (string)trim($objWorksheet->getCellByColumnAndRow($col, $row)->getValue());
			}

			$res = [];
			foreach ($tableData as $v) {
				if (empty($v)) {
					$res[] = $v;
				}
			}

			if (count($res) != count($tableData)) {
				yield $tableData;
			}
		}
	}
}