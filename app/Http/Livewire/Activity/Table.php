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
    public $description = '';
    public $model = '';
    public $content = '';
    
    public $sortBy = 'id';
    public $sortDesc = true;

    public function render()
    {
        
        ini_set('memory_limit', '10000M');
        ini_set('memory_limit', '-1');
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
            'description' => $this->description,
            'model' => $this->model,
            'content' => trim($this->content),
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
