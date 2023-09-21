<?php

namespace App\Http\Livewire\Activity;

use Livewire\Component;
use Livewire\WithPagination;
use App\Repositories\ActivityRepository;
use PhpParser\Node\Stmt\Echo_;

class Table extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $pageSize = 50;
    public $date = '';
    public $name = '';
    public $model = '';
    public $content = '';
    public $search = '';
    public $sortBy = 'id';
    public $sortDesc = true;

    public function render()
    {
        return view('livewire.activity.table',[
            'activities' => $this->getActivities(),
            'models' => $this->getModels()
        ]);
    }


    public function getActivities()
    {
        return (new ActivityRepository)->get(request()->merge([
            'date' => $this->date,
            'name' => $this->name,
            'model' => $this->model,
            'content' => $this->content,
            'search' => $this->search,
        ]),true,$this->pageSize,$this->sortBy,$this->sortDesc ? 'DESC' : 'asc');
    }

    function getModels(){
        $path = app_path() . "/Models";
        $out = [];
        $results = scandir($path);
        
        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;
                $out[] = substr($result,0,-4);
        }
        return $out;
    }

    
}
