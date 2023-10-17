<div class="form-row">
    <div class="form-holder form-holder-2">
        <label for="mail_host">Mail Host <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="mail_host" name="mail_host" required placeholder="smtp.gmail.com">
    </div>
</div>

<div class="form-row">
    <div class="form-holder form-holder-2">
        <label for="mail_port">Mail Port <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="mail_port" name="mail_port" required value="465">
    </div>
</div>

<div class="form-row">
    <div class="form-holder form-holder-2">
        <label for="mail_username">Mail Username <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="mail_username" name="mail_username" autocomplete="off" required>
    </div>
</div>

<div class="form-row">
    <div class="form-holder form-holder-2">
        <label for="mail_password">Mail Password <span class="text-danger">*</span></label>
        <input type="password" class="form-control" id="mail_password" name="mail_password" autocomplete="off" required>
    </div>
</div>

<div class="form-row">
    <div class="form-holder form-holder-2">
        <label for="mail_encryption">Mail Encryption <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="mail_encryption" name="mail_encryption" autocomplete="off" required>
    </div>
</div>

<div class="form-row">
    <div class="form-holder form-holder-2">
        <label for="mail_from">Mail From Address <span class="text-danger">*</span></label>
        <input type="email" class="form-control" id="mail_from" name="mail_from" autocomplete="off" required>
    </div>
</div>

<div class="form-row">
    <div class="form-holder form-holder-2">
        <label for="">Check SMTP</label>
        <button type="button" class="button-default" id="btnCheckSMTP">Check Connection</button>
    </div>
</div>