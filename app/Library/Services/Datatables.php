<?php
namespace App\Library\Services;

class Datatables
{
    public $token;

    protected $config;

    protected $attributes;

    public function __construct()
    {
        $this->token = csrf_token();
    }

    public function config(Array $config)
    {
        $this->config = $config;
        return $this;
    }

    public function options(Array $opt)
    {
        $data_post = ['_token' => $this->quotes($this->token)];

        if(isset($opt['params']))
        {
            foreach($opt['params'] as $key => $val)
            {
                $params[$key] = $this->quotes($val);
            }

            $data_post = array_merge($params, $data_post);
        }

        $this->attributes = json_decode(json_encode([
            'data' => [
                'type' => $this->quotes('remote'),
                'source' => [
                    'read' => [
                        'url' => $this->quotes($opt['url'] ?? ''),
                        'method' => $this->quotes($opt['method'] ?? 'POST'),
                        'params' => $data_post
                    ]
                ],
                'pageSize' => $opt['pageSize'] ?? 10,
                'serverPaging' => $opt['serverPaging'] ?? TRUE,
                'serverFiltering' => $opt['serverFiltering'] ?? TRUE,
                'serverSorting' => $opt['serverSorting'] ?? TRUE
            ],
            'layout' => [
                'scroll' => $opt['scroll'] ?? FALSE,
                'footer' => $opt['footer'] ?? FALSE
            ],
            'sortable' => $opt['sortable'] ?? TRUE,
            'pagination' => $opt['pagination'] ?? TRUE,
            'search' => [
                'input' => "$('#dt_search_query')",
                'key' => $this->quotes('generalSearch')
            ]
        ]));

        return $this;
    }

    public function columns(Array $column = [])
    {
        $args = [];
        foreach($column as $_column)
        {
            if(isset($_column['template']))
            {
                $_column['template'] = 'function(data){ '.trim(preg_replace('/\s+/', ' ', rtrim($_column['template'],';'))).'; }';
            }

            foreach($_column as $key => $_col)
            {
                if($key!='template')
                {
                    $_column[$key] = $this->quotes($_col);
                }
            }

            $args[] = $_column;
        }

        $this->attributes = array_merge((array) $this->attributes, ['columns' => json_decode(json_encode($args))]);
        return $this;
    }

    protected function fetch_edit()
    {
        $updates = isset($this->config['updates']) && is_array($this->config['updates']) ? $this->config['updates'] : [];
        $template = NULL;
        if(count($updates) > 0)
        {
            foreach($updates as $val)
            {
                if(isset($val['data']) && is_array($val['data']))
                {
                    $template .= '<div class="kt_datatable_filter_update mr-2">';
                        $template .= '<select class="form-control selectpicker" data-width="'.($val['width'] ?? '200px').'" data-style="btn-'.($val['color'] ?? 'primary').' btn-sm" data-url="'.($val['url'] ?? '').'"><option value="" data-icon="select-icon flaticon-edit-1"> '.$val['title'].'</option>';
                        foreach($val['data'] as $data)
                        {
                            $template .= '<option value="'.$data['id'].'" data-icon="select-icon flaticon-more-v6"> '.$data['value'].'</option>';
                        }
                        $template .= '</select>';
                    $template .= '</div>';
                }
            }
        }

        return $template;
    }

    public function table(Array $config = [], $selector = FALSE)
    {
        $template = NULL;

        if(isset($config['search']) && $config['search'] == TRUE)
        {
            $template .= '
            <div class="mb-7">
                <div class="row align-items-center">
                    <div class="col-md-6 col-xxl-4 my-2 my-md-0">
                        <div class="d-xxl-flex">
                            <button class="btn btn-light-success font-weight-bold mr-2" type="button" id="kt_datatable_reload"><i class="flaticon2-refresh"></i> Reload Data</button>
                            <div class="input-icon flex-grow-1">
                                <input type="text" class="form-control" placeholder="Search..." id="dt_search_query" name="search" autocomplete="off" />
                                <span>
                                    <i class="flaticon2-search-1 text-muted"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        }

        if(isset($config['selector']) && $config['selector'] == TRUE)
        {
            $template .= '
            <div class="mt-10 mb-5 collapse" id="kt_datatable_group_action_form">
                <div class="d-flex align-items-center">
                    <div class="font-weight-bold text-danger mr-3">Selected
                    <span id="kt_datatable_selected_records">0</span> records:</div>';

            if(isset($this->config['roles']['update']) && $this->config['roles']['update'])
            {
                $template .= $this->fetch_edit();
            }

            if(isset($this->config['roles']['delete']) && $this->config['roles']['delete'])
            {
                $template .= '
                    <button class="btn btn-sm btn-danger mr-2" type="button" id="kt_datatable_delete_all">
                        <span class="svg-icon svg-icon-md">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"></path>
                                    <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
                                </g>
                            </svg>
                        </span>
                        Delete Selected Data
                    </button>';
            }
            $template .= '
                </div>
            </div>';
        }

        $template .= '<div class="'.($config['class'] ?? 'datatable datatable-bordered datatable-head-custom').'" id="'.(isset($this->config['table']) ? ltrim($this->config['table'],'#') : 'kt-datatable').'"></div>';

        return $template;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function scripts()
    {
        $checkbox = isset($this->config['checkbox']) && $this->config['checkbox'] == FALSE ? '!1' : '!0';

        $attributes = $this->json_encode_advanced((array) $this->attributes);

        $script  = '<script type="text/javascript">';
        $script .= '"use strict";';
        $script .= 'var Datatables = (function () {';
            $script .= 'var t = ';
                $script .= ($attributes);
            $script .= ';';
            $script .= 'return {
                load: function () {
                    !(function () {
                        (t.extensions = { checkbox: '.($checkbox).' });
                        var e = $("'.($this->config['table'] ?? '#kt-datatable').'").KTDatatable(t);
                        var confirmModal = $("#confirmModal");
                        var alertModal = $("#alertModal");
                        var deleteUrl = "'.($this->config['delete_url'] ?? '').'";
                        var updateRole = '.(isset($this->config['roles']['update']) && $this->config['roles']['update'] ? 'true' : 'false').';
                        var deleteRole = '.(isset($this->config['roles']['delete']) && $this->config['roles']['delete'] ? 'true' : 'false').';
                        e.on("datatable-on-check datatable-on-uncheck", function (t) {
                            if(deleteRole==true || updateRole==true){
                                var a = e.rows(".datatable-row-active").nodes().length;
                                $("#kt_datatable_selected_records").html(a), a > 0 ? $("#kt_datatable_group_action_form").collapse("show") : $("#kt_datatable_group_action_form").collapse("hide");
                            }
                        }),
                        e.on("datatable-on-layout-updated", function (t,x) {
                            $("#kt_datatable_reload").attr("data-initial-text", "<i class=\'flaticon2-refresh\'></i> Reload Data").attr("data-loading-text", "<i class=\'fas fa-spinner spinner mr-8\'></i> Loading...");
                            if(deleteRole==false && updateRole==false){
                                $(this).find("label.checkbox > input:checkbox").map(function(){
                                    $(this).parents("label.checkbox").addClass("checkbox-disabled");
                                    $(this).prop("disabled",true);
                                });
                            }
                            $(this).find("label.checkbox > input:checkbox").map(function(){
                                var $parent=$(this).parents(".datatable-row"),id=$parent.find("label.checkbox > input:checkbox").val();
                                $parent.find(".btn-action-edit").attr("href", $parent.find(".btn-action-edit").attr("href") + $(this).val());
                                $parent.find(".btn-action-sort").attr("data-id", $(this).val());
                            });
                        }),
                        e.on("datatable-on-ajax-done", function (t,x) {
                            var btn_reload = $("#kt_datatable_reload"),
                            initialText = btn_reload.attr("data-initial-text"),
                            loadingText = btn_reload.attr("data-loading-text");
                            btn_reload.html(initialText).removeClass("disabled").prop("disabled",false);
                        }),
                        e.on("datatable-on-ajax-fail", function (t,x) {
                            var btn_reload = $("#kt_datatable_reload"),
                            initialText = btn_reload.attr("data-initial-text"),
                            loadingText = btn_reload.attr("data-loading-text");
                            btn_reload.html(initialText).removeClass("disabled").prop("disabled",false);
                            alertModal.find(".modal-title > span").text("ERROR");
                            alertModal.find(".modal-body").text(x.responseJSON.message ? x.responseJSON.message : "Something error when processing the data");
                            alertModal.modal("show");
                        }),
                        $(".kt_datatable_filter_update select").on("change", function(){
                            var dt=e,a = e
                                .rows(".datatable-row-active")
                                .nodes()
                                .find(\'.checkbox > [type="checkbox"]\')
                                .map(function (t, e) {
                                    return $(e).val();
                                });
                            var $this=$(this);
                            var id = [];
                            a.map(function(i,v){
                                id.push(v);
                            });
                            loader.init();
                            $("#kt_datatable_group_action_form").collapse("hide");
                            $("label.checkbox > input:checkbox").prop("checked",false);
                            $(".datatable-row.datatable-row-active").removeClass("datatable-row-active");
                            $.post($this.attr("data-url"), {_token: "'.$this->token.'", id: id, update_id: $this.val()}, function(e){
                                loader.destroy();
                                $this.val("");
                                $this.selectpicker("render");
                                if(!e.status){
                                    alertModal.find(".modal-title > span").text("ERROR");
                                    alertModal.find(".modal-body").text(e.message);
                                    alertModal.modal("show");
                                    return false;
                                }
                                dt.reload();
                            }).fail(function(xhr) {
                                loader.destroy();
                                alertModal.find(".modal-title > span").text("ERROR");
                                alertModal.find(".modal-body").text(xhr.responseJSON.message);
                                alertModal.modal("show");
                            });
                        }),
                        $("#kt_datatable_delete_all").on("click", function() {
                            var a = e
                                .rows(".datatable-row-active")
                                .nodes()
                                .find(\'.checkbox > [type="checkbox"]\')
                                .map(function (t, e) {
                                    return $(e).val();
                                });
                            confirmModal.find(".modal-title > span").text("Warning : Delete Data");
                            confirmModal.find(".modal-body").text("This action will delete this data permanently!");
                            confirmModal.modal("show");

                            var id = [];
                            a.map(function(i,v){
                                id.push(v);
                            });

                            confirmModal.find(".btn-modal-action").click(function(){
                                var btn = $(this),
                                initialText = btn.attr("data-initial-text"),
                                loadingText = btn.attr("data-loading-text");
                                btn.html(loadingText).addClass("disabled").prop("disabled",true);
                                $.post(deleteUrl, {_token: "'.$this->token.'", id: id}, function(e){
                                    confirmModal.modal("hide");
                                    if(!e.status){
                                        alertModal.find(".modal-title > span").text("ERROR");
                                        alertModal.find(".modal-body").text(e.message);
                                        alertModal.modal("show");
                                        return false;
                                    }
                                    alertModal.find(".modal-title > span").text("INFO");
                                    alertModal.find(".modal-body").text(e.message);
                                    alertModal.find(".btn-modal-cancel").removeAttr("data-dismiss");
                                    alertModal.modal("show");

                                    alertModal.find(".btn-modal-cancel").click(function(){
                                        document.location.reload();
                                    });
                                }).fail(function(xhr) {
                                    confirmModal.modal("hide");
                                    alertModal.find(".modal-title > span").text("ERROR");
                                    alertModal.find(".modal-body").text(xhr.responseJSON.message);
                                    alertModal.modal("show");
                                });
                            });
                        }),
                        $("#kt_datatable_reload").on("click", function() {
                            var btn = $(this),
                            initialText = btn.attr("data-initial-text"),
                            loadingText = btn.attr("data-loading-text");
                            btn.html(loadingText).addClass("disabled").prop("disabled",true);
                            e.reload();
                        })
                    })()
                }
            };';
        $script .= '})();';
        $script .= 'jQuery(document).ready(function () {
            Datatables.load();
        });';
        $script .= '</script>';

        return $script;
    }

    protected function quotes($args)
    {
        $str = $args;
        if(is_string($args))
        {
            $str = sprintf('"%s"', $args);
        }

        if(is_numeric($args) || is_integer($args))
        {
            $str = (int) $args;
        }

        if(is_array($args) || is_object($args))
        {
            foreach($args as $_key => $_args)
            {
                $str[$_key] = $this->quotes($_args);
            }
        }

        return $str;
    }

    protected function json_encode_advanced($arr, $sequential_keys = false, $quotes = false, $beautiful_json = false, $type = '')
    {
        $output = $type == 'array' ? "[" : "{";
        $count = 0;
        foreach ($arr as $key => $value) {
            if ( $this->isAssoc((array) $arr) || (!$this->isAssoc((array) $arr) && $sequential_keys == true ) ) {
                $output .= ($quotes ? '"' : '') . $key . ($quotes ? '"' : '') . ' : ';
            }

            if (is_array($value) || is_object($value)) {
                $output .= $this->json_encode_advanced((array) $value, $sequential_keys, $quotes, $beautiful_json, gettype($value));
            } else if (is_bool($value)) {
                $output .= ($value ? 'true' : 'false');
            } else if (is_numeric($value)) {
                $output .= $value;
            } else {
                $output .= ($quotes || $beautiful_json ? '"' : '') . $value . ($quotes || $beautiful_json ? '"' : '');
            }

            if (++$count < count((array) $arr)) {
                $output .= ', ';
            }

        }

        $output .= $type == 'array' ? "]" : "}";

        return $output;
    }

    protected function isAssoc(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
