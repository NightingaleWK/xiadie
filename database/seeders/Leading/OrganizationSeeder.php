<?php

namespace Database\Seeders\Leading;

use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 创建预定义的组织结构
        $this->seedPredefinedOrganizations();
    }

    /**
     * 填充预定义的组织结构数据
     */
    private function seedPredefinedOrganizations(): void
    {
        // 创建顶级分类：企业
        $enterprise = $this->createOrganization('企业', null);

        // 创建企业下的子组织
        $zhaoyuan = $this->createOrganization('招远中电智慧产业发展有限公司', $enterprise->id);
        $this->createOrganization('运维管理部', $zhaoyuan->id);
        $this->createOrganization('综合管理部', $zhaoyuan->id);
        $this->createOrganization('财务管理部', $zhaoyuan->id);
        $this->createOrganization('软件研发部', $zhaoyuan->id);

        // 其他企业
        $this->createOrganization('中国电子', $enterprise->id);
        $this->createOrganization('中国电子云', $enterprise->id);
        $this->createOrganization('华为', $enterprise->id);
        $this->createOrganization('海康威视', $enterprise->id);
        $this->createOrganization('大华', $enterprise->id);

        // 创建顶级分类：政府
        $government = $this->createOrganization('政府', null);

        // 创建政府下的子组织
        $shandong = $this->createOrganization('山东省', $government->id);
        $yantai = $this->createOrganization('烟台市', $shandong->id);
        $zhaoyuanCity = $this->createOrganization('招远市', $yantai->id);

        // 创建招远市下的政府部门
        $this->createOrganization('应急管理局', $zhaoyuanCity->id);
        $this->createOrganization('市委办', $zhaoyuanCity->id);
        $this->createOrganization('公安局', $zhaoyuanCity->id);
        $this->createOrganization('环保局', $zhaoyuanCity->id);
        $this->createOrganization('银监局', $zhaoyuanCity->id);
    }

    /**
     * 创建单个组织
     *
     * @param string $name 组织名称
     * @param int|null $parentId 父组织ID
     * @return Organization 创建的组织
     */
    private function createOrganization(string $name, ?int $parentId): Organization
    {
        $code = $this->generateCode($name);

        return Organization::create([
            'name' => $name,
            'code' => $code,
            'description' => $name . '组织描述',
            'parent_id' => $parentId,
            'is_active' => true,
        ]);
    }

    /**
     * 为组织名称生成简单的代码
     *
     * @param string $name 组织名称
     * @return string 生成的代码
     */
    private function generateCode(string $name): string
    {
        // 提取拼音首字母或使用随机字符串
        $code = 'ORG_' . strtoupper(substr(md5($name), 0, 6));

        // 确保代码唯一
        $count = Organization::where('code', 'like', $code . '%')->count();
        if ($count > 0) {
            $code .= '_' . ($count + 1);
        }

        return $code;
    }
}
