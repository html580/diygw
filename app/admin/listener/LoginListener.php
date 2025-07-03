<?php
declare (strict_types = 1);

namespace app\admin\listener;

use app\log\model\LoginsModel;

class LoginListener
{
    public function handle($params)
    {
        $agent = request()->header('user-agent');
        LoginsModel::create([
            'username' => $params['username'],
            'ipaddr'   => request()->ip(),
            'browser'    => getBrowser($agent),
            'login_location'    => getIpLocation(request()->ip()),
            'os'         => getOs($agent),
            'status'     => $params['status']
        ]);
    }
}
