<?php namespace App\Cases;

use App\Lookup\EloquentRepository as LookupRepo;
use App\Sop\Checklist;
use App\Sop\Phase;
use App\Sop\RepositoryInterface as SopRepo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EloquentRepository implements RepositoryInterface {

    /**
     * @type case
     */
    private $case;

    /**
     * @type Phase
     */
    private $phase;

    /**
     * @type SopRepo
     */
    private $sop;
    /**
     * @var LookupRepo
     */
    private $lookupRepo;

    function __construct(Cases $case, Phase $phase, SopRepo $sop, LookupRepo $lookupRepo)
    {

        $this->case = $case;
        $this->phase = $phase;
        $this->sop = $sop;
        $this->lookupRepo = $lookupRepo;
    }

    public function all($keyword = null)
    {
        return $this->case->orderBy('created_at', 'desc')->get();
    }

    public function byJaksa($jaksaId)
    {
        return $this->case->published()->where('jaksa_id', '=', $jaksaId)->get();
    }

    public function create($input, $user)
    {

        $case = $this->case->create($input);

        $defaultPhase = $this->phase->where('case_type_id', '=', $case->type_id)->orderBy('ordinal')->first();

        if($defaultPhase)
        {
            $case->phase()->associate($defaultPhase)->save();

            // auto checklist SPDP
            if(isset($input['spdp_date']) && isset($input['spdp_number']))
            {
                $checklist = Checklist::where('phase_id', '=', $defaultPhase->id)->whereOrdinal(1)->first();

                if($checklist)
                {
                    $data['date'] = $input['spdp_diterima_date'];
                    $data['note'] = 'Checklist otomatis ketika register kasus';
                    $this->sop->addChecklist($case, $checklist, $data);
                }
            }

        }
        else
        {
            return false;
        }

        $case->author()->associate($user)->save();

        return $case;
    }

    public function update($id, $input)
    {
        return $this->case->findOrFail($id)->update($input);
    }

    public function find($id)
    {
        return $this->case->findOrFail($id);
    }

    public function delete($id)
    {
        return $this->case->findOrFail($id)->delete();
    }

    public function search($keyword, $type = null, $includeDraft = false)
    {
        $query = $this->case->orderBy('updated_at', 'DESC');

        if(!$includeDraft)
        {
            $query->published();
        }

        if($type)
        {
            $query->where('type_id', '=', $type);
        }

        $ids = DB::table('suspects')->join('cases_suspects', 'cases_suspects.suspects_id', '=', 'suspects.id')->select('cases_suspects.cases_id')->where('suspects.name', 'LIKE', '%'.$keyword.'%')->get();
        $cases_ids = array();
        foreach($ids as $t){
            $cases_ids[] = $t->cases_id;
        }



        if($keyword)
        {
            $query->where(function($query2) use ($keyword, $cases_ids){
                $query2->where('kasus', 'LIKE', '%'.$keyword.'%')->orWhere('spdp_number', 'LIKE', '%'.$keyword.'%')->orWhereIn('id', $cases_ids);

            });
        }

        return $query->paginate();
    }

    public function activities($case)
    {
        $activities = [];
        foreach($case->activities as $activity)
        {
            $activities[] = [
                'date_for_human' => $activity['date_for_human'],
                'date'  => $activity['date'],
                'name'  => $activity['title'],
                'note'  => $activity['content']
            ];
        }

        return $activities;
    }

    public function addActivity($case, $attributes)
    {
        $attributes['date'] = Carbon::now()->toDateString();
        return $case->activities()->create($attributes);
    }

    public function statisticByPhase($year, $type)
    {
        $json = [];
        $phases = $this->sop->byType(explode(',', $type));

        //initialization
        foreach(range(1,12) as $month)
        {
            $data = ['month' => Carbon::createFromDate(null, $month, null)->formatLocalized('%B'), 'year' => $year];
            foreach($phases as $phase)
            {
                $data[$phase->name] = 0;
            }
            $json[$month] = $data;
        }

        $stat = DB::table('v_monthly_case_phase')
            ->select([DB::raw('count(case_id) count'), 'phase_id', 'month'])
            ->where('year', '=', $year)
            ->groupBy(['phase_id', 'month'])
            ->get();

        foreach(range(1,12) as $month)
        {
            foreach($phases as $phase)
            {
                $id = $phase->id;
                $name = $phase->name;

                $data = array_first($stat, function($key, $element) use ($month, $id){

                    if($element->month == $month && ($element->phase_id == $id))
                    {
                        return true;
                    }
                });

                if($data)
                {
                    $json[$month][$name] = (int) $data->count;
                }
            }
        }

        $series = [];
        foreach($phases as $phase)
        {
            $series[] = [
                'valueField'    => $phase->name,
                'name'          => $phase->name,
                'color'         => $phase->color
            ];
        }

        return ['series' => $series, 'data' => array_values($json)];
    }

    public function statisticByStatus($year)
    {
        $json = [];

        //initialization
        foreach(range(1,12) as $month)
        {
            $json[$month]['month'] = Carbon::createFromDate(null, $month, null)->formatLocalized('%B');
            $json[$month]['year'] = $year;
            $json[$month]['open'] = 0;
            $json[$month]['close'] = 0;
        }

        $openCases = $this->case
            ->select([
                    DB::raw('COUNT(1) as count'),
                    DB::raw('MONTH(start_date) as month'),
                ])
            ->whereRaw('YEAR(start_date) = ' . $year)
            ->groupBy([DB::raw('MONTH(start_date)')])
            ->get();

        $closedCases = $this->case
            ->select([
                    DB::raw('COUNT(1) as count'),
                    DB::raw('MONTH(start_date) as month'),
                ])
            ->whereRaw('YEAR(start_date) = ' . $year)
            ->whereNotNull('finish_date')
            ->groupBy([DB::raw('MONTH(finish_date)')])
            ->get();


        foreach(range(1,12) as $month)
        {
            $openCase = array_first($openCases, function($key, $element) use ($month){

                if($element['month'] == $month)
                {
                    return true;
                }
            });

            if($openCase)
            {
                $json[$month]['open'] = $openCase['count'];
            }

            $closedCase = array_first($closedCases, function($key, $element) use ($month){

                if($element['month'] == $month)
                {
                    return true;
                }
            });

            if($closedCase)
            {
                $json[$month]['close'] = $closedCase['count'];
            }
        }

        return array_values($json);
    }

    public function statisticByCategory($year, $type)
    {
        $json = [];

        $categories = $this->lookupRepo->categoryPidum();

        //initialization
        foreach(range(1,12) as $month)
        {
            $json[$month]['month'] = Carbon::createFromDate(null, $month, null)->formatLocalized('%B');
            $json[$month]['year'] = $year;

            foreach($categories as $key => $value)
            {
                $json[$month][$key] = 0;
            }
        }

        $stat = DB::table('cases')
                  ->select([DB::raw('count(id) count'), 'category', DB::raw('MONTH(start_date) month')])
                  ->whereRaw("YEAR(start_date) = $year")
                  ->whereNotNull('category')
                  ->groupBy(['category', 'month'])
                  ->get();


        foreach($stat as $row)
        {
            $json[$row->month][$row->category] = $row->count;
        }

        $series = [];
        $colors = \Config::get('color');
        $i = 1;

        foreach($categories as $key => $val)
        {
            $series[] = [
                'valueField'    => $key,
                'name'          => $val,
                'color'         => $colors[array_rand(array_slice($colors, $i - 1, 1), 1)]
            ];
            $i++;
        }

        return ['series' => $series, 'data' => array_values($json)];
    }

    public function countActive()
    {
        return $this->case->whereStatus(Cases::STATUS_ONGOING)->count();
    }

    public function countNewToday()
    {
        return $this->case->where('start_date', '=', (new Carbon())->toDateString())->count();
    }

    public function countNewThisWeek()
    {
        return $this->case->where('start_date', '>=', (new Carbon())->subWeek()->toDateString())->count();
    }

    public function countNewThisMonth()
    {
        return $this->case->where('start_date', '>=', (new Carbon())->subDays(30)->toDateString())->count();
    }

    public function sidangToday()
    {
        return $this->case->where('persidangan_date', '=', (new Carbon())->toDateString())->get();
    }

    public function upcomingSidang()
    {
        return $this->case->where('persidangan_date', '>=', (new Carbon())->toDateString())->orderBy('persidangan_date')->get();
    }
}

