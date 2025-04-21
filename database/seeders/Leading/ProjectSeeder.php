<?php

namespace Database\Seeders\Leading;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::create([
            'name' => '天网二期',
            'name_en' => 'Skynet Phase Two',
            'code' => 'TWEQ',
            'description' => '天网二期项目',
            'project_manager' => '孙靖雨',
            'manager_phone' => '18666929925',
            'client_name' => '招远市公安局',
            'client_contact' => '彭启耀',
            'client_phone' => '18660069889',
            'status' => 'operation',
        ]);

        Project::create([
            'name' => '城管一包',
            'name_en' => 'Urban Management Package One',
            'code' => 'CGYB',
            'description' => '城管一包项目',
            'project_manager' => '张军',
            'manager_phone' => '13884672709',
            'client_name' => '招远市综合行政执法局',
            'client_contact' => '王文清',
            'client_phone' => '15653557989',
            'status' => 'operation',
        ]);

        Project::create([
            'name' => '城管二包',
            'name_en' => 'Urban Management Package Two',
            'code' => 'CGEB',
            'description' => '城管二包项目',
            'project_manager' => '董晓宁',
            'manager_phone' => '13954563802',
            'client_name' => '招远市综合行政执法局 招远市自然资源和规划局',
            'client_contact' => '衣作连',
            'client_phone' => '13176906691',
            'status' => 'operation',
        ]);

        Project::create([
            'name' => '城管三包',
            'name_en' => 'Urban Management Package Three',
            'code' => 'CGSB',
            'description' => '城管三包项目',
            'project_manager' => '马海龙（调岗）',
            'manager_phone' => '15064665296',
            'client_name' => '招远市人民防空办公室',
            'client_contact' => '丛兵',
            'client_phone' => '13953567918',
            'status' => 'operation',
        ]);

        Project::create([
            'name' => '交通一期',
            'name_en' => 'Traffic Management Phase One',
            'code' => 'JTYQ',
            'description' => '交通一期项目',
            'project_manager' => '刘伟（调岗）',
            'manager_phone' => '18801172527',
            'client_name' => '交警大队',
            'client_contact' => '王忠磊',
            'client_phone' => '15153582220',
            'status' => 'operation',
        ]);

        Project::create([
            'name' => '交通二期',
            'name_en' => 'Traffic Management Phase Two',
            'code' => 'JTEQ',
            'description' => '交通二期项目',
            'project_manager' => '张军强（调岗）',
            'manager_phone' => '17659906502',
            'client_name' => '交警大队',
            'client_contact' => '王忠磊',
            'client_phone' => '15153582220',
            'status' => 'operation',
        ]);

        Project::create([
            'name' => '市监一期',
            'name_en' => 'Market Supervision Phase One',
            'code' => 'SJYQ',
            'description' => '市监一期项目',
            'project_manager' => '王欣',
            'manager_phone' => '18553287775',
            'client_name' => '招远市市监局',
            'client_contact' => '杨学军',
            'client_phone' => '18561025568',
            'status' => 'operation',
        ]);

        Project::create([
            'name' => '矿山一期',
            'name_en' => 'Mining Safety Phase One',
            'code' => 'KSYQ',
            'description' => '矿山一期项目',
            'project_manager' => '董晓宁',
            'manager_phone' => '13954563802',
            'client_name' => '招远市自然资源和规划局；招远市应急管理局；招远市公安局矿区治安派出所',
            'client_contact' => '杨君风',
            'client_phone' => '13706458550',
            'status' => 'operation',
        ]);

        Project::create([
            'name' => '安全一期',
            'name_en' => 'Safety Management Phase One',
            'code' => 'AQYQ',
            'description' => '安全一期项目',
            'project_manager' => '孙靖雨',
            'manager_phone' => '18666929925',
            'client_name' => '招远市公安局',
            'client_contact' => '王文松',
            'client_phone' => '13686386669',
            'status' => 'operation',
        ]);

        Project::create([
            'name' => '旅游一期',
            'name_en' => 'Tourism Management Phase One',
            'code' => 'LYYQ',
            'description' => '旅游一期项目',
            'project_manager' => '李曜',
            'manager_phone' => '15668062999',
            'client_name' => '招远市文化和旅游服务中心',
            'client_contact' => '程刚',
            'client_phone' => '18553509331',
            'status' => 'operation',
        ]);

        Project::create([
            'name' => '教育一期',
            'name_en' => 'Education Informatization Phase One',
            'code' => 'JYYQ',
            'description' => '教育一期项目',
            'project_manager' => '索超',
            'manager_phone' => '15866462646',
            'client_name' => '招远市教育和体育局',
            'client_contact' => '盛振哲',
            'client_phone' => '18300517828',
            'status' => 'operation',
        ]);

        Project::create([
            'name' => '二中一期',
            'name_en' => 'No.2 Middle School Phase One',
            'code' => 'EZYQ',
            'description' => '二中一期项目',
            'project_manager' => '姚朋松',
            'manager_phone' => '15949711892',
            'client_name' => '招远市教体局',
            'client_contact' => '盛振哲',
            'client_phone' => '18300517828',
            'status' => 'operation',
        ]);

        Project::create([
            'name' => '政务一期',
            'name_en' => 'E-Government Phase One',
            'code' => 'ZWYQ',
            'description' => '政务一期项目',
            'project_manager' => '马龙',
            'manager_phone' => '18153552357；15253100472',
            'client_name' => '招远市人民政府',
            'client_contact' => '刘玉芳',
            'client_phone' => '18854523012',
            'status' => 'operation',
        ]);

        Project::create([
            'name' => '智慧环保',
            'name_en' => 'Smart Environmental Protection',
            'code' => 'HBYQ',
            'description' => '智慧环保项目',
            'project_manager' => '李振杰',
            'manager_phone' => '18660583068',
            'client_name' => '招远市环保局',
            'client_contact' => '郝海庆',
            'client_phone' => '18660538818',
            'status' => 'operation',
        ]);

        Project::create([
            'name' => '天网机房',
            'name_en' => 'Skynet Server Room',
            'code' => 'TWJF',
            'description' => '天网机房项目',
            'project_manager' => '孙靖雨',
            'manager_phone' => '18666929925',
            'client_name' => '招远市公安局',
            'client_contact' => '彭启耀',
            'client_phone' => '18660069889',
            'status' => 'operation',
        ]);

        Project::create([
            'name' => '指挥中心',
            'name_en' => 'Command Center',
            'code' => 'ZHZX',
            'description' => '指挥中心项目',
            'project_manager' => '马龙',
            'manager_phone' => '18153600000',
            'client_name' => '招远市大数据中心',
            'client_contact' => '张豪杰',
            'client_phone' => '18660073960',
            'status' => 'operation',
            'start_date' => '2020-01-01',
            'operation_date' => '2023-01-01',
        ]);

        Project::create([
            'name' => '天网三期',
            'name_en' => 'Skynet Phase Three',
            'code' => 'TWSQ',
            'description' => '天网三期项目',
            'status' => 'operation',
        ]);

        Project::create([
            'name' => '交通三期',
            'name_en' => 'Traffic Management Phase Three',
            'code' => 'JTSQ',
            'description' => '交通三期项目',
            'status' => 'operation',
        ]);

        Project::create([
            'name' => '智慧社区一期',
            'name_en' => 'Smart Community Phase One',
            'code' => 'ZHSQYQ',
            'description' => '智慧社区一期项目',
            'status' => 'operation',
        ]);

        Project::create([
            'name' => '综合治理一期',
            'name_en' => 'Comprehensive Governance Phase One',
            'code' => 'ZHZLYQ',
            'description' => '综合治理一期项目',
            'status' => 'operation',
        ]);
    }
}
