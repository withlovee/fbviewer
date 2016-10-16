<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Excel;
use DateTimeZone;

class CommentController extends Controller
{
	public function index(Request $request)
	{
		$search = $request->input('search');
		$user = $request->input('user');
		$daterange = $request->input('daterange');
		$export = $request->input('export');

		if($daterange) {
			$delimeter = strpos($daterange, ' - ');

			$start_date = substr($daterange, 0, $delimeter);
			$end_date = substr($daterange, $delimeter + 3);
		} else {
			$start_date = '19/08/2016 00:00';
			$end_date = strftime('%d/%m/%Y %H:%M');
		}

		$query = $this->get_query($search, $user, $start_date, $end_date);
		
		$count = $query->count();

		if($export) {
			$comments = $query->get();
			// return response()
			// 	->view('comment/table', array('comments' => $comments))
			// 	->header("Content-Type", "application/vnd.ms-excel; charset=utf-8")
			// 	->header("Content-Disposition", "attachment; filename=facebook_comments.xls")
			// 	->header("Expires", "0")
			// 	->header("Cache-Control", "must-revalidate, post-check=0, pre-check=0")
			// 	->header("Cache-Control", "private");
			// return $this->get_excel($comments);
			return view('comment/table', array(
				'count' => $count,
				'comments' => $comments
			));
		} else {
			$comments = $query->paginate(100);
		}

		return view('comment/index', array(
			'count' => $count,
			'comments' => $comments,
			'search' => $search,
			'user' => $user,
			'start_date' => $start_date,
			'end_date' => $end_date,
			'daterange' => $daterange,
			'url' => $request->fullUrl(),
			'export' => $export
		));
	}

	public function delete(Request $request) {
		$return_url = $request->input('return_url');

		if(!$return_url) {
			$return_url = '/';
		}

		$ids = $request->input('ids');

		if($ids) {
			foreach($ids as $id) {
				Comment::where('id', $id)->update(['active' => false]);
			}
		}

		return redirect($return_url);
	}

	private function get_query($search, $user, $start_date, $end_date) {

		$format = 'd/m/Y H:i P';

		$query = Comment::where('active', '!=', false)
				->where('message', 'regex', "/.*$search/")
				->where('from.name', 'regex', "/.*$user/i")
				->where('created_time', '>=', date_create_from_format($format, $start_date.' +0700'))
				->where('created_time', '<=', date_create_from_format($format, $end_date.' +0700'));

		return $query->orderBy('created_time', 'desc');
	}

	private function get_excel($comments) {
		$excel = Excel::create('excel', function($excel) use ($comments) {
			$excel->sheet('export_excel', function($sheet) use ($comments) {

				$sheet->row(1, array('Comment ID','Facebook User ID','Name','Message','Date','Attachment'));
				$count = 2;

				foreach($comments as $comment) {

                    $date = $comment['created_time']->toDateTime();
                    $date = $date->setTimezone(new DateTimeZone('Asia/Bangkok'));
					
					if (isset($comment['attachment']) && isset($comment['attachment']['media']['image']['src'])) {
						$attachment = $comment['attachment']['media']['image']['src'];
					} else {
						$attachment = '';
					}
					$sheet->cell('A'.$count, function($cell) use ($comment) {
						$cell->setValue($comment['id']);
					});
					$sheet->cell('B'.$count, function($cell) use ($comment) {
						$cell->setValue($comment['from']['id']);
					});
					$sheet->cell('C'.$count, function($cell) use ($comment) {
						$cell->setValue($comment['from']['name']);
					});
					$sheet->cell('D'.$count, function($cell) use ($comment) {
						// $cell->setValue($comment['message']);
						// $cell->setValue(mb_convert_encoding(html_entity_decode($comment['message']), 'HTML-ENTITIES', 'UTF-8'));
						if(mb_detect_encoding($comment['message']) == 'ASCII') {
							//$cell->setValue('error!!!');
							$cell->setValue($comment['message']);
						} else {
							$cell->setValue(trim(mb_convert_encoding($comment['message'],'UTF-8')));
						}
						//$cell->setValue(mb_detect_encoding($comment['message']));
					});
					$sheet->cell('E'.$count, function($cell) use ($date) {
						$cell->setValue($date->format('Y-m-d H:i:s P'));
					});
					$sheet->cell('F'.$count, function($cell) use ($attachment) {
						$cell->setValue($attachment);
					});
					$count++;
				}

			});
		})->export('xls');

		return $excel;
	}

}