<?php

use Illuminate\Database\Seeder;

class GoodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $a = [];
        for($i=0;$i<100;$i++){
            $data['title']=str_random(6);
            $data['num']=rand(100,999);
        	$data['price']=rand(10000,99999);
        	$data['content']=str_random(100);
        	$data['addtime']=date('y-m-d h:i:s');
        	$a[] = $data;
        }
        DB::table('goods')->insert($a);
    }
}
