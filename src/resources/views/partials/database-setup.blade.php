<div class="form-row">
    <div class="form-holder form-holder-2">
        <label for="db_host">Database Host <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="db_host" name="db_host" required placeholder="localhost / 127.0.0.1">
    </div>
</div>

<div class="form-row">
    <div class="form-holder form-holder-2">
        <label for="db_port">Database Port <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="db_port" name="db_port" required value="3306">
    </div>
</div>

<div class="form-row">
    <div class="form-holder form-holder-2">
        <label for="db_database">Database Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="db_database" name="db_database" autocomplete="off" required>
    </div>
</div>

<div class="form-row">
    <div class="form-holder form-holder-2">
        <label for="db_username">Database Username <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="db_username" name="db_username" autocomplete="off" required>
    </div>
</div>

<div class="form-row">
    <div class="form-holder form-holder-2">
        <label for="db_password">Database Password <span class="text-danger">*</span></label>
        <input type="password" class="form-control" id="db_password" name="db_password" autocomplete="off" required>
    </div>
</div>

<div class="form-row">
    <div class="form-holder form-holder-2">
        <label for="db_password">Check Connection</label>
        <button type="button" class="button-default" id="btnCheckConnection">Check Connection</button>
    </div>
</div>