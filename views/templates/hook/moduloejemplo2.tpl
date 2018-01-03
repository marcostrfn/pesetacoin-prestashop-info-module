<p>{l s='Hola mundoo'}</p>
<p>{$image_baseurl}</p>
<p>aa{$my_module_name}</p>

{if isset($my_module_name) && $my_module_name}
           {$my_module_name}
       {else}
           World
       {/if}
	   
	   <img src="{$image_baseurl}/ptc.png"/>
	   