@foreach($list as $k=>$v)
    <tr @if($newid==$v['id']) class="self-tr" @endif>
    <td>{{$k+1}}</td>
    <td>{{$v['nickname']}}</td>
    <td>{{$v['score']}}%</td>
    <td>{{$v['add_date']}}</td>
    </tr>
@endforeach



@if($myrecord)
    <tr class="self-tr">
        <td>{{$myrecord->rownum}}</td>
        <td>{{$myrecord->nickname}}</td>
        <td>{{$myrecord->score}}%</td>
        <td>{{$myrecord->add_date}}</td>
    </tr>
@endif
