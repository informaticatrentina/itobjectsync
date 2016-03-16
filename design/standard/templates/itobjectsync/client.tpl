<div class="row dashboard">
    <div class="col-md-6 col-md-offset-3">
        <div class="card-material">
            <header>
                <h2>
                    <i class="fa fa-tags"></i>
                    Tematiche da Sincronizzare<br/>
                    <small>
                        Sorgente: {$repository_url}
                    </small>
                </h2>
            </header>
            <hr/>
            <p>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            {def $default_dest_node=fetch( 'content', 'node', hash( 'node_id', $default_dest_node_id ) )}
                            {if is_set($default_dest_node)}
                                Destinazione: 
                                <b>Home/{$default_dest_node.path_identification_string}</b>
                            {else}
                                <em>Seleziona la destinazione</em>
                            {/if}
                        </div>
                        <div class="col-md-6 text-right">
                            <button name="SelezionaDestinazione" type="submit" class="btn btn-primary text-right">
                                <i class="fa fa-folder"></i>
                                Seleziona Destinazione
                            </button>
                        </div>
                    </div>
                        
                    {if is_set($default_dest_node)}
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="header">Tematica</th>
                                    <th class="header">Sincronizza</th>
                                </tr>
                            </thead>
                            <tbody>
                            {foreach $remote_tags as $tag}
                                <tr>
                                    <td>{$tag.Keyword}</td>
                                    <td>
                                        {if $tematiche|contains($tag.Keyword)}
                                            <button name="DisableTag_{$tag.ID}" type="submit" class="btn btn-danger" value="{$tag.Keyword}">
                                                <i class="fa fa-times"></i>
                                                Disattiva
                                            </button>
                                        {else}
                                            <button name="EnableTag_{$tag.ID}" type="submit" class="btn btn-success" value="{$tag.Keyword}">
                                                <i class="fa fa-check"></i>
                                                Attiva
                                            </button>
                                        {/if}

                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    {/if}
                </form>
            </p>
        </div>
    </div>
</div>
