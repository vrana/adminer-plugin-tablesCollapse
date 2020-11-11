<?php

/**
 * Faz a colapsacao das tabelas com divisor "__". Tipo phpmyadmin
 *
 * @author Tiago Marques
 */
class AdminerTablesCollapse
{
	private $separator = "";
	private $group_translations = true;
	private $translation_sufixes = [];

	function __construct($separator = "__",$group_translations = true,$translation_sufixes = ["__translation","_translation","__translations","_translations"]) {
		$this->separator = $separator;
		$this->group_translations = $group_translations;
		$this->translation_sufixes = $translation_sufixes;
	}

	function head() {
		?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"<?= nonce() ?>></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"<?= nonce() ?>></script>
		<script type="text/javascript"<?= nonce() ?>>
			let cookie_collapse = [];
			$(document).ready(function() {
				if ($.cookie("adminer_table_collapse") !== undefined)
					cookie_collapse = JSON.parse($.cookie("adminer_table_collapse"));

				$("[data-collapse-source]").on("click", function(evt) {
					evt.preventDefault();
					let source = $(this).attr("data-collapse-source");
					let target = $("[data-collapse-target='"+(source)+"']");
					let collapsed = target.attr("data-collapsed");

					if (collapsed === "true") {
						target.attr("data-collapsed", "false")

						// coloca o elemento no cookie
						cookie_collapse[cookie_collapse.length] = source;
					} else {
						target.attr("data-collapsed", "true")

						// remove o elemento do cookie
						let final_cookie = [];
						for (let i = 0; i < cookie_collapse.length; i++) {
							if (cookie_collapse[i] !== source) {
								final_cookie[final_cookie.length] = cookie_collapse[i];
							}
						}
						cookie_collapse = final_cookie;
					}

					$.cookie("adminer_table_collapse", JSON.stringify(cookie_collapse), {expires: 999999, path: "/"});

					return false;
				});
			});
		</script>

		<style type="text/css">
			#menu div.line { padding: 0; margin: 5px 0; cursor: pointer; border: none; }
				#menu [data-collapse-target] { margin: 5px 0 5px 20px; }
				#menu [data-collapse-source] { cursor: pointer; }
					#menu .package { display: inline-block; position: relative; width: 16px; height: 16px; background: transparent url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAFo9M/3AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QkQ5QjA4N0I4NDc4MTFFQTk5RjVDNjQ4MTQ4QjY4MEUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QkQ5QjA4N0M4NDc4MTFFQTk5RjVDNjQ4MTQ4QjY4MEUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpCRDlCMDg3OTg0NzgxMUVBOTlGNUM2NDgxNDhCNjgwRSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpCRDlCMDg3QTg0NzgxMUVBOTlGNUM2NDgxNDhCNjgwRSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PjhIhxQAAAFlSURBVHjaYvz//z8DCDAB8R8QAyCAGJFFwAAggBiAIv+AeCIQmwHxE5CMChDbAbEAEB8DCCC4HhgAqXgKxLxADJL5D1PBArXmP0jFKiB2AeILQKwMEEAgW5SAeNV/CKgCYm4g7oOKKYGM+AM1AgQ4oEb9BjkaZiQy+APFjDABDGeiA4AAIqgAZEU6EF8E4rdArA3EnkB8ACrOAPLFYiC+CsS+INOAeDfURwtBfHQr/sNcD3MoEy63IbuBoCPTgPgVEHdBxbqB+DVUnHA4AAQYugKQiUbQyLIFYm4g/grEh4F4DxCfA+J/6BpAJuwEYkOo5FMofgNNPYbQ8NMCYgkgNoEa9h/mAuTI1obaDsJWQPwCiMWB+CgQ+6GFECQoQQZAIwAds0AjBMa/B8SzkPj/YRGF7AJkwIKU7LABsAtYoOEwF4h3Q/32Bk+gi0K954ovFgyRwsEYiM9CDQbh8+ixQDCeCQEArdja3b2FrIcAAAAASUVORK5CYII=) no-repeat center center; background-size: contain; vertical-align: middle; margin-right: 5px; border: none; }
					#menu .relations { display: inline-block; position: relative; width: 16px; height: 16px; background: transparent url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAFo9M/3AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RTFBMTAxMDM4NDdDMTFFQUJEQ0I5OTcxODI4OTAxQTYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RTFBMTAxMDQ4NDdDMTFFQUJEQ0I5OTcxODI4OTAxQTYiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFMUExMDEwMTg0N0MxMUVBQkRDQjk5NzE4Mjg5MDFBNiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFMUExMDEwMjg0N0MxMUVBQkRDQjk5NzE4Mjg5MDFBNiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PifEFVIAAABVSURBVHjaYvz//z8DCDAxQAFAADFiiAAEEFwEBpgY0ABAABFWQVgAIIAwzCCog/oKAAJoMLhhMCgACDCC4UCxDYQAC5QmxxmMVHHBwBvAguyfAXEBAJvFFiaJbjHJAAAAAElFTkSuQmCC) no-repeat center center; background-size: contain; vertical-align: middle; margin-right: 5px; }
				#menu [data-collapsed="true"] { display: none; }
					#menu [data-collapsed="true"] span.open { display: inline-block; }
					#menu [data-collapsed="true"] span.close { display: none; }
				#menu [data-collapsed="false"] { display: block; }
					#menu [data-collapsed="false"] span.open { display: none; }
					#menu [data-collapsed="false"] span.close { display: inline-block; }
		</style>
		<?php
	}

	function tablesPrint(array $tables) {
		$actions = [$_GET["select"],$_GET["edit"],$_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"]];

		if($this->group_translations) {
			$tables = $this->tables_group_translations($tables);
		}

		$groups = $this->group_tables($tables); // agrupa as tabelas

		// vai buscar o cookie
		$cookie = (!isset($_COOKIE["adminer_table_collapse"])) ? [] : json_decode($_COOKIE["adminer_table_collapse"]);

		// recursivamente, preenche a coluna
		foreach($groups as $group => $g)
			$this->tables_print_line($g,$group,$actions,$cookie,"");

		return true;
	}

	function tables_print_line($line,$group_name,$actions,$cookie,$path) {
		// verifica se e grupo, e se tem mais do que apenas um elemento
		$is_group = false;
		if(substr((string)$group_name,0,1) == "_") {
			$is_group = true;

			// se for grupo mas se tiver apenas um elemento
			if(count($line) == 1) {
				$line = $line[0];
				$is_group = false;
			}
		}

		if($is_group) { // GRUPO
			$group_name = ltrim($group_name,'_'); // se for grupo tem o apendice "_", portanto, retira-o
			$path .= $group_name;

			// verifica se a tabela contem o nome do grupo
			$active = false;
			if(strpos($this->get_actual_table_name(),$path) === 0) // apenas se o nome da tabela estiver no inicio da path
				$active = true;

			// procura no cookie tambem, para ver se algum bloco esta aberto
			foreach($cookie as $c)
				if($c == Adminer::database().$path)
					$active = true;

			echo "<div class='line' data-collapse-source='".(Adminer::database().$path)."'>";
				echo '<span class="package"></span>';
				echo '<a href="#">'.($group_name).'</a>';
			echo '</div>';

			echo '<div data-collapse-target="'.(Adminer::database().$path).'" data-collapsed="'.($active ? "false" : "true").'">';
			foreach($line as $group_name => $gtmp)
				$this->tables_print_line($gtmp,$group_name,$actions,$cookie,$path."__");
			echo '</div>';
		} else { //LINHA FINAL
			$name = Adminer::tableName($line);
			if($name == "")
				return;

			$active = in_array($name,$actions);

			if(support("table") || support("indexes")) {
				echo '<div class="line">';
					echo '<a class="relations" href="'.h(ME).'select='.urlencode($line["Name"]).'" title="Browser"></a>';
					echo '<a href="'.h(ME).'table='.urlencode($name).'"'.bold($active,(is_view($line) ? "view" : ""))."' title=\"Select\" data-link='main'>$name</a>";
				echo '</div>';
			}
		}
	}

	/**
	 * Agrupa todos os elementos com divisao do separador, de forma em arvore
	 *
	 * @param array $tables
	 *
	 * @return array
	 */
	function group_tables(array $tables) {
		$groups = [];

		// cria o array de grupos
		foreach($tables as $table => $status) {
			$cur = &$groups;

			$s1 = explode($this->separator,$table);
			foreach($s1 as $s) {
				if(!isset($cur["_".$s])) {
					$cur["_".$s] = [];
				}

				if($s == $s1[count($s1) - 1]) {
					$cur["_".$s] []= $status;
				}

				$cur = &$cur["_".$s];
			}
		}
		return $groups;
	}

	/**
	 * Ordena as tabelas de traducao, juntamente com as tabelas de origem
	 *
	 * @param array $tables
	 *
	 * @return array
	 */
	function tables_group_translations(array $tables) {
		$final = [];
		$already_exists = [];
		foreach($tables as $index => $actual_table) {
			if(!in_array($actual_table["Name"],$already_exists)) {
				$final[$index] = $actual_table;
				$actual = $actual_table["Name"];

				// vai procurar a sua respectiva tabela de traducao
				foreach($tables as $indextranslation => $ttranslation) {
					foreach($this->translation_sufixes as $sufix) {// procura nos diferentes sufixos
						if($ttranslation["Name"] == $actual.$sufix) {
							$already_exists []= $ttranslation["Name"]; // coloca como ja existente
							$final[$indextranslation] = $ttranslation;
						}
					}
				}
			}
		}
		return $final;
	}

	/**
	 * Devolva o nome da tabela actual
	 * @return string
	 */
	function get_actual_table_name() {
		$table = "";

		if(isset($_GET["table"]))
			$table = $_GET["table"];

		if(isset($_GET["select"]))
			$table = $_GET["select"];

		if(isset($_GET["create"]))
			$table = $_GET["create"];

		if(isset($_GET["edit"]))
			$table = $_GET["edit"];

		if(isset($_GET["indexes"]))
			$table = $_GET["indexes"];

		if(isset($_GET["foreign"]))
			$table = $_GET["foreign"];

		if(isset($_GET["trigger"]))
			$table = $_GET["trigger"];

		return $table;
	}
}
