<?php namespace App\Http\Controllers;

use App\Lookup\RepositoryInterface as LookupRepository;
use App\Menu\RepositoryInterface as MenuRepository;
use App\Sop\RepositoryInterface;
use Illuminate\Http\Request;
use App\Cases\RepositoryInterface as CasesRepository;
use App\Officer\RepositoryInterface as OfficerRepository;

class FrontendController extends Controller {

    public function getIndex(MenuRepository $menuRepository)
    {
        $menu = $menuRepository->all();
        return view('frontend.index', compact('menu'));
    }

    public function getSearch(Request $request, CasesRepository $repository, RepositoryInterface $sop, LookupRepository $lookup)
    {
        $keyword = $request->get('q');
        $type = $request->get('type');

        $cases = $repository->search($keyword, $type);
        $phases = $sop->byType($type);

        $types = $lookup->lists('kasus', '--Pilih Jenis Kasus--');

        return view('frontend.search', compact('cases', 'phases', 'type', 'keyword', 'types'))->with('page', 'search')->with('keyword',$keyword);
    }

    public function getOfficer(OfficerRepository $officer)
    {
        $officers = $officer->all();
        return view('frontend.officer', compact('officers'))->with('page', 'officer');
    }
}