<?php /* #?ini charset="utf-8"?
#----------------------------------------------------------------------------------------------
# ATTENZIONE
#
# per questioni di ordine di caricamento dei file .ini questo append non viene considerato
# ed è stato messo nella cartella override dell'installazione di ez 
#----------------------------------------------------------------------------------------------

[ImportSettings]
AvailableSourceHandlers[]=ObjectImportHandler
AvailableSourceHandlers[]=ITTematicaSyncHandler


[ObjectImportHandler-HandlerSettings]
# Indicates if handler is enabled or not. Mandatory. Must be "true" or "false"
Enabled=true
# Intelligible name
Name=Object Import Handler
# Class for source handler. Must implement ISQLIImportSourceHandler and extend SQLIImportAbstractSourceHandler
ClassName=ObjectImportHandler
# Facultative. Indicates whether debug is enabled or not
Debug=enabled
# Same as [ImportSettings]/DefaultParentNodeID, but source handler specific
DefaultParentNodeID=2
# StreamTimeout, handler specific. If empty, will take [ImportSettings]/StreamTimeout
StreamTimeout=
# Below you can add your own settings for your source handler


[ITTematicaSyncHandler-HandlerSettings]
# Indicates if handler is enabled or not. Mandatory. Must be "true" or "false"
Enabled=true
# Intelligible name
Name=Import Automatico Tematiche Handler
# Class for source handler. Must implement ISQLIImportSourceHandler and extend SQLIImportAbstractSourceHandler
ClassName=ITTematicaSyncHandler
# Facultative. Indicates whether debug is enabled or not
Debug=enabled
# Same as [ImportSettings]/DefaultParentNodeID, but source handler specific
DefaultParentNodeID=2
# StreamTimeout, handler specific. If empty, will take [ImportSettings]/StreamTimeout
StreamTimeout=
# Below you can add your own settings for your source handler


*/ ?>
