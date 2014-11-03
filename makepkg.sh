#!/bin/bash

SOURCE="${BASH_SOURCE[0]}"
while [ -h "$SOURCE" ]; do
    DIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"
    SOURCE="$(readlink "$SOURCE")"
    [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE"
done
DIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"

INSTALL_DIR="$DIR/../opencart"
UPLOAD_DIR="$DIR/upload"

FILES=(
    "catalog/controller/module/rossko.php"
    "catalog/model/module/rossko.php"
    "catalog/view/theme/default/template/module/rossko.tpl"
    "catalog/view/theme/default/template/module/rossko_results.tpl"
    "catalog/view/theme/default/stylesheet/rossko.css"
    "admin/controller/module/rossko.php"
    "admin/view/template/module/rossko.tpl"
)

for FILE in "${FILES[@]}"; do
    DIR="$( dirname "$FILE" )"

    if [ ! -d "$UPLOAD_DIR/$DIR" ]; then
      mkdir -p "$UPLOAD_DIR/$DIR"
    fi

    echo "> $FILE"
    cp -ar "$INSTALL_DIR/$FILE" "$UPLOAD_DIR/$FILE"
done

echo "All ready, Sir!"
