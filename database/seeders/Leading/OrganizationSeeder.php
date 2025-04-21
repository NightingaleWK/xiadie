<?php

namespace Database\Seeders\Leading;

use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 创建几个顶级组织
        $rootOrganizations = Organization::factory()
            ->root()
            ->count(3)
            ->create()
            ->each(function (Organization $organization) {
                // 为每个顶级组织创建第二层组织
                $this->createChildren($organization, 2, 3);
            });
    }

    /**
     * 递归创建子组织
     * 
     * @param Organization $parent 父组织
     * @param int $depth 当前深度
     * @param int $maxDepth 最大深度
     * @param int $maxChildren 每层最大子组织数量
     */
    private function createChildren(Organization $parent, int $depth, int $maxDepth, int $maxChildren = 3): void
    {
        if ($depth > $maxDepth) {
            return;
        }

        // 为当前层级创建1到maxChildren个子组织
        $childCount = rand(1, $maxChildren);
        Organization::factory()
            ->childOf($parent)
            ->count($childCount)
            ->create()
            ->each(function (Organization $child) use ($depth, $maxDepth, $maxChildren) {
                // 递归创建下一层子组织，随机决定是否继续创建子组织
                if (rand(0, 10) > 3) { // 70%概率继续创建子组织
                    $this->createChildren($child, $depth + 1, $maxDepth, $maxChildren);
                }
            });
    }
}
