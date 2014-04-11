<?php

namespace BWC\Share\App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DataTablesAdapter
{
    /** @var string */
    public $echo;

    /** @var string */
    public $search;

    /** @var int */
    public $pageSize;

    /** @var int */
    public $offset;

    /** @var int */
    public $pageNum;

    /** @var array columnIndex=>ASC|DESC */
    public $sortIndexInfo;

    /** @var array columnName=>bool */
    public $sortInfo;


    /**
     * @param Request $request  Where to read input parameters from
     * @param array $columns   Optional list of column names from table need to build sortInfo, if omitted only sortIndexInfo would be available
     */
    function bind(Request $request, array $columns = null) {
        $this->echo = $request->get('sEcho');
        $this->search = $request->get('sSearch');
        $this->pageSize = intval($request->get('iDisplayLength'));
        $this->offset = intval($request->get('iDisplayStart'));
        $this->pageNum = $this->pageSize ? floor($this->offset / $this->pageSize)+1 : 1;
        $this->sortIndexInfo = array();
        for ($i=0; $i<10; $i++) {
            $key = 'iSortCol_'.$i;
            if (!$request->query->has($key)) break;
            $sortColIdx = intval($request->get($key));
            $sortDir =  strtoupper($request->get('sSortDir_0'));
            if ($sortDir != 'ASC' && $sortDir != 'DESC') {
                $sortDir = 'ASC';
            }
            $this->sortIndexInfo[] = array($sortColIdx, $sortDir);
            if ($columns && isset($columns[$sortColIdx])) {
                $this->sortInfo[$columns[$sortColIdx]] = $sortDir == 'ASC';
            }
        }
    }



    /**
     * @param object[] $array
     * @param int|bool $totalRecords
     * @return Response
     */
    function getResponse(array $array, $totalRecords = false) {
        $data = new \stdClass();
        if ($totalRecords) {
            $data->iTotalDisplayRecords = $totalRecords;
            $data->iTotalRecords = $totalRecords;
        } else {
            $data->iTotalDisplayRecords = count($array);
            $data->iTotalRecords = count($array);
        }
        //$data->iTotalRecords = $totalRecords === false ? $data->iTotalDisplayRecords : $totalRecords; // SELECT FOUND_ROWS()
        $data->sEcho = $this->echo;
        $data->aaData = $array;
        $json = json_encode($data);
        return new Response($json, 200, array('Content-Type'=>'application/json'));
    }

}