<div class="box-body">
    <table class="table">
        <tr>
            <td>PHP  {{$output['required_php']}} <small style="padding:1px 3px;border-radius:5px;background:#32a7d7;color:white;">Found {{ $output['php_version'] }}</small></td>
            <td>
                @if($output['php'])
                    <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                @else
                    <i class="fa fa-close text-danger" aria-hidden="true"></i>
                @endif
            </td>
        </tr>

        <tr>
            <td>OpenSSL PHP Extension</td>
            <td>
                @if($output['openssl'])
                    <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                @else
                    <i class="fa fa-close text-danger" aria-hidden="true"></i>
                @endif
            </td>
        </tr>

        <tr>
            <td>PDO PHP Extension</td>
            <td>
                @if($output['pdo'])
                    <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                @else
                    <i class="fa fa-close text-danger" aria-hidden="true"></i>
                @endif
            </td>
        </tr>

        <tr>
            <td>Mbstring PHP Extension</td>
            <td>
                @if($output['mbstring'])
                    <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                @else
                    <i class="fa fa-close text-danger" aria-hidden="true"></i>
                @endif
            </td>
        </tr>

        <tr>
            <td>Tokenizer PHP Extension</td>
            <td>
                @if($output['tokenizer'])
                    <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                @else
                    <i class="fa fa-close text-danger" aria-hidden="true"></i>
                @endif
            </td>
        </tr>

        <tr>
            <td>XML PHP Extension</td>
            <td>
                @if($output['xml'])
                    <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                @else
                    <i class="fa fa-close text-danger" aria-hidden="true"></i>
                @endif
            </td>
        </tr>

        <tr>
            <td>cURL PHP Extension</td>
            <td>
                @if($output['curl'])
                    <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                @else
                    <i class="fa fa-close text-danger" aria-hidden="true"></i>
                @endif
            </td>
        </tr>

        <tr>
            <td>zip PHP Extension</td>
            <td>
                @if($output['zip'])
                    <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                @else
                    <i class="fa fa-close text-danger" aria-hidden="true"></i>
                @endif
            </td>
        </tr>

        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>

        <tr>
            <td><b>{{storage_path()}}</b> is writable?</td>
            <td>
                @if($output['storage_writable'])
                    <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                @else
                    <i class="fa fa-close text-danger" aria-hidden="true"></i>
                @endif
            </td>
        </tr>

        <tr>
            <td><b>{{base_path('bootstrap/cache')}}</b> is writable?</td>
            <td>
                @if($output['cache_writable'])
                    <i class="fa fa-check-circle-o text-success" aria-hidden="true"></i>
                @else
                    <i class="fa fa-close text-danger" aria-hidden="true"></i>
                @endif
            </td>
        </tr>

    </table>
</div>