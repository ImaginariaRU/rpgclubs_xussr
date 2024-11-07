<div style="text-align: left" class="section-infobox-header">
    <h1 class="section-infobox-title">{$poi.title}</h1>

    <button
            id="actor-infobox-close"
            class="section-infobox-button-close">X</button>
</div>

<div id="section-infobox-content" class="section-infobox-content">
    {if $poi.banner_horizontal}
        <img src="{$poi.banner_horizontal}" data-width="100%" alt="VK banner">
    {/if}


    <div class="infobox-desc">
        {$poi.description}
    </div>

    <br/>

    <div class="infobox-address">
        <strong>Адрес:</strong><br/> <span style="color: #3388ff">{$poi.address}</span>
    </div>

    {if $poi.address_hint}
        <strong>Как добраться?</strong><br><br>
        {$poi.address_hint}
    {/if}

    <br>

    <div class="infobox-site">
        <a href="{$poi.url_site}" target="_blank">{$poi.url_site}</a>
    </div>
</div>
<span style="font-size: xx-small; float: left"><a href="{Arris\AppRouter::getRouter('form.add.ticket', [ 'id' => $poi.id ])}" onclick="window.location.hash=''; return true;">Complain</a></span>
<span style="font-size: xx-small; float: right">Escape - закрыть это окно</span>