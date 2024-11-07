<div style="text-align: left" class="section-infobox-header">
    <h1 class="section-infobox-title">{$dataset.title}</h1>

    <button
            id="actor-infobox-close"
            class="section-infobox-button-close">X</button>
</div>

<div id="section-infobox-content" class="section-infobox-content">
    {if $dataset.banner_horizontal}
        <img src="{$dataset.banner_horizontal}" data-width="100%" alt="VK banner">
    {/if}


    <div class="infobox-desc">
        {$dataset.description}
    </div>

    <br/>

    <div class="infobox-address">
        <strong>Адрес:</strong><br/> <span style="color: #3388ff">{$dataset.address}</span>
    </div>

    {if $dataset.address_hint}
        <strong>Как добраться?</strong><br><br>
        {$dataset.address_hint}
    {/if}

    <br>

    <div class="infobox-site">
        <a href="{$dataset.url_site}" target="_blank">{$dataset.url_site}</a>
    </div>
</div>
<span style="font-size: xx-small; float: left"><a href="{Arris\AppRouter::getRouter('form.add.ticket', [ 'id' => $dataset.id ])}" onclick="window.location.hash=''; return true;">Complain</a></span>
<span style="font-size: xx-small; float: right">Escape - закрыть это окно</span>