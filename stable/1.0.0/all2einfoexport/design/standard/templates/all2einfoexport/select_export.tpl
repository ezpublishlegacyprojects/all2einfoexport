{literal}
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/calendar/assets/skins/sam/calendar.css" />
<!-- Combo-handled YUI JS files: --> 
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/calendar/calendar-min.js"></script>
{/literal}

{def $lastexport = false}
{def $count = 0}

<form method="post" action="{'/all2einfoexport/export'|ezurl(no)}">

<div class="context-block yui-skin-sam">
    <div class="box-header">
        <div class="box-tc">
            <div class="box-ml">
                <div class="box-mr">
                    <div class="box-tl">
                        <div class="box-tr">
                            <div>
                                <h1 class="context-title">Collected Information Export</h1>
                            </div>
                            <div class="header-mainline"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box-ml">
        <div class="box-mr">
            <div class="box-content">
            	<div class="block">
		    <p>
			Please select the information collection to export:
		    </p>
		</div>            
                <table class="list" cellspacing="0">
                    <tbody>
			<tr>
			    <th class="tight"></th>
			    <th>Name</th>
			    <th>ObjectID</th>
			    <th>Collections</th>
			    <th>Last export</th>
			</tr>
                    {foreach $availableCollections as $collection}
                    	{set $count = $collection.contentobject_id|countcollections}
                    	{set $lastexport = $collection.contentobject_id|lastexport}
                    	
                    	
                        <tr class="bglight">
                            <td><input type="radio" name="SelectedCollection" value="{$collection.contentobject_id}"></td>
                            <td>{$collection.name}</td>
                            <td>{$collection.contentobject_id}</td>
                            <td><a href={concat('/infocollector/collectionlist/',$collection.contentobject_id)|ezurl()}>{$count}</a></td>
                            <td>{if $lastexport}{$lastexport|l10n( 'shortdatetime' )}{/if}</td>
                        </tr>
                    {/foreach}    
                    </tbody>
                </table>
            </div>
            <div class="box-content">
            	<div class="block">
		    <p>
			Please select the export range. You can either select a start and end date to export for the selected collection or
			select all collections since the last export.
		    </p>
		</div>
		
		
            	<table class="list" cellspacing="0">
		<tbody>
		<tr class="bglight">
			<td><input type="radio" name="Range" value="false"></td>
			<td colspan="2">Export collections since last export</td>
		</tr>				
		<tr class="bglight">
			<td><input type="radio" name="Range" value="true"></td>
			<td>Start Date:<div id="cal1Container"></div></td>
			<td>End date:<div id="cal2Container"></div></td>
		</tr>		
                </tbody>
                </table>
                
                <div class="block">
                <input type="hidden" id="exportFrom" name="exportFrom" value="false">
                <input type="hidden" id="exportTo" name="exportTo" value="false">
                <input class="button" name="ExportSelected" value="Export Selected" type="submit">
                </div>

            </div>
        </div>
    </div>
    <div class="controlbar">
        <div class="box-bc">
            <div class="box-ml">
                <div class="box-mr">
                    <div class="box-tc">
                        <div class="box-bl">
                            <div class="box-br">
                                &nbsp;
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



{literal}
	<script>
		YAHOO.namespace("example.calendar");
		
		function mySelectHandler1(type,args,obj) {
        var selected = args[0];
        var selDate = this.toDate(selected[0]);
        var miStr = Date.parse(selDate) / 1000;
        document.getElementById('exportFrom').value = miStr;
    };
		function mySelectHandler2(type,args,obj) {
        var selected = args[0];
        var selDate = this.toDate(selected[0]);
        var miStr = Date.parse(selDate) / 1000 + 86400;
        document.getElementById('exportTo').value = miStr;
    };
		
		YAHOO.example.calendar.init = function() {
			YAHOO.example.calendar.cal1 = new YAHOO.widget.Calendar("cal1","cal1Container");
			YAHOO.example.calendar.cal2 = new YAHOO.widget.Calendar("cal2","cal2Container");
			YAHOO.example.calendar.cal1.selectEvent.subscribe(mySelectHandler1, YAHOO.example.calendar.cal1, true);
			YAHOO.example.calendar.cal2.selectEvent.subscribe(mySelectHandler2, YAHOO.example.calendar.cal2, true);
			YAHOO.example.calendar.cal1.render();
			YAHOO.example.calendar.cal2.render();
		}
		
		YAHOO.util.Event.onDOMReady(YAHOO.example.calendar.init);
	</script>
{/literal}


</form>
