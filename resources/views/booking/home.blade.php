@extends('layouts.app')

@section('content')

    {!! Html::script('/js/visualcalendar.js') !!}
<div class="container-fluid">
    <h1>Réservation</h1>
    @if(Session::has('successMessage'))
        <div class="alert alert-success">
            {{Session::get('successMessage')}}
        </div>
    @elseif(Session::has('errorMessage'))
        <div class="alert alert-danger">
            {{Session::get('errorMessage')}}
        </div>
    @endif
    <div style="width:510px;color:#000;margin:auto;">
        <iframe height="85" frameborder="0" width="510" scrolling="no" src="http://www.prevision-meteo.ch/services/html/chavornay-vd/horizontal" allowtransparency="true"></iframe>
        <a style="text-decoration:none;font-size:0.75em;" title="Prévisions à 4 jours pour Chavornay (VD)" href="http://www.prevision-meteo.ch/meteo/localite/chavornay-vd">Prévisions à 4 jours pour Chavornay (VD)</a>
    </div>
    <ul class="nav nav-tabs">
        @foreach($courts as $key=>$court)
            <li class="@if( Session::has('currentCourt')) @if(Session::get('currentCourt')== $court->id){{'active'}}@endif @elseif($key == 0) active @endif" ><a data-toggle="tab" href="#tab-{{$court->id}}">{{$court->name}}</a></li>
        @endforeach
    </ul>
    <div class="tab-content">
        @foreach($courts as $key=>$court)
            <div id="tab-{{$court->id}}"  class="tab-pane fade @if( Session::has('currentCourt')) @if(Session::get('currentCourt')== $court->id){{'in active'}}@endif @elseif($key == 0) in active @endif">
            </div>
            <script>
                var vc{{$court->id}} = new VisualCalendar();
                vc{{$court->id}}.config={!! \App\Reservation::getVcConfigJSON($court->id, 'div#tab-'.$court->id) !!};
                vc{{$court->id}}.build();
                vc{{$court->id}}.generate();
                vc{{$court->id}}.ev.onSelect=function(elem, datetime){
                    var myDate = parseDate(datetime);
                    $("#fkCourt").val({{$court->id}});
                    $("#modal-resume").html('Réservation du court N° '+$("#fkCourt").val()+' le ' +myDate.getUTCDate().toStringN(2)+ "-" + (myDate.getMonth() + 1).toStringN(2) + "-" + myDate.getFullYear()+' à '+myDate.getHours().toStringN(2) + ":" + myDate.getMinutes().toStringN(2) );
                    $("#reservation-date").val(datetime+':00');
                    $('#reservation-modal').modal('show');
                }
                vc{{$court->id}}.ev.onPlanifClick=function(elem, planif){
                    console.log(elem, planif);
                }
            </script>
        @endforeach


    <!-- Modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="reservation-modal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method="post" role="form" method="POST" action="{{ url('/booking')}}">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="myModalLabel">Réservation</h4>
              </div>
              <div class="modal-body">
                  <div id="modal-resume" class="notice notice-info">
                      <p>Réservation du court .. le 'date' </p>
                  </div>
                  {{ csrf_field() }}
                  {{ method_field('POST') }}

                  <input type="hidden" id="reservation-date" name="dateTimeStart">
                  <input type="hidden" id="fkCourt" name="fkCourt" value=1>
                  <input type="hidden" id="page" name="page" value='booking'>
                  @if (Auth::check())
                    Choisissez votre adversaire
                    <select name="fkWithWho">
                      @foreach($membersList as $member)
                        <option value="{{$member->id}}">{{$member->firstname}} {{$member->lastname}}</option>
                      @endforeach
                    </select>
                  @endif

                  <div id="modal-panel"></div>
                  <div id="modal-content"></div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                  <button type="submit" id="booking" class="btn btn-success">
                      <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                      Réserver
                  </button>
              </div>
          </form>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>



    <style media="screen">
        .vc-cnt {
            box-sizing: border-box;
            width: 100%;
            overflow: auto;
            font-family: Arial,sans-serif;
            position: relative;
        }
        table {
            width: 100%;
            text-align: center;
            border-collapse: collapse;
            box-sizing: border-box;
        }
        td {
            border: 1px solid black;
            box-sizing: border-box;
        }
        td.vc-table-head {
            background-color: #999;
            color: #eee;
        }
        tr>td:nth-child(1) {
            color: #5a5a5a;
            font-size: 10px;
            vertical-align: top;
        }
        tr>td.vc-table-head:nth-child(1) {
            color: #eee;
            font-size: 14px;
            vertical-align: middle;
        }
        tr>td {
            border-bottom-color: #ccc;
            background-color: #fff;
            height: 23px;
        }
        tr:last-child>td {
            border-bottom-color: black;
        }
        tr>td:first-child {
            min-width: 70px;
            max-width: 70px;
            width: 70px;
        }
        .vc-selected{
            background-color: #9cbcf7;
        }
        .vc-clickable{
            cursor: pointer;
            transition-duration: 250ms;
        }
        .vc-clickable:not(.vc-selected):hover{
            background-color: #c5d9ff;
        }
        .vc-nomoreselect .vc-clickable{
            cursor: default;
        }
        .aucune{background-color: #afa;}
        .simple2{background-color: #ffa;}
        .vc-passed{filter: brightness(0.9);}
        .vc-own-planif{filter: hue-rotate(-60deg);}
    </style>


{{--    {!! Html::script('/ajax/calendar.js') !!}
    {!! Html::script('/ajax/booking.js') !!}--}}
@endsection
