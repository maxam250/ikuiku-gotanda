<?php

use App\Models\Member;

if (!function_exists('searchMembers')) {
    /**
     * 全会員の一覧を取得する
     * 
     * @param string|null $keyword 検索キーワード
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function searchMembers($keyword = null)
    {
        $query = Member::select('id', 'member_no', 'name', 'tel', 'point');
        
        if ($keyword) {
            // 暗号化されていないフィールドでの検索
            $query->where(function($q) use ($keyword) {
                $q->where('id', 'like', "%{$keyword}%")
                  ->orWhere('member_no', 'like', "%{$keyword}%");
            });
            
            // 暗号化されたフィールドでの検索
            $members = $query->get();
            $filteredMembers = collect();
            
            foreach ($members as $member) {
                $match = false;
                
                // 名前検索
                try {
                    $decryptedName = \Illuminate\Support\Facades\Crypt::decryptString($member->getRawOriginal('name'));
                    if (strpos($decryptedName, $keyword) !== false) {
                        $match = true;
                    }
                } catch (\Exception $e) {
                    // 暗号化されていない場合
                    if (strpos($member->name, $keyword) !== false) {
                        $match = true;
                    }
                }
                
                // 電話番号検索
                if (!$match) {
                    try {
                        $decryptedTel = \Illuminate\Support\Facades\Crypt::decryptString($member->getRawOriginal('tel'));
                        if (strpos($decryptedTel, $keyword) !== false) {
                            $match = true;
                        }
                    } catch (\Exception $e) {
                        // 暗号化されていない場合
                        if (strpos($member->tel, $keyword) !== false) {
                            $match = true;
                        }
                    }
                }
                
                if ($match) {
                    $filteredMembers->push($member);
                }
            }
            
            return $filteredMembers->take(10);
        }
        
        return $query->orderBy('id', 'asc')->get();
    }
} 