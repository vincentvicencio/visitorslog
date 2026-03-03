<div class="currentlocation">
    <div class="panel">
        <div class="header">
            <div class="maintitle">enter current location</div>
            <div class="subtitle">Please specify where you are currently located</div>
        </div>
        <form method="POST" action="">
            <select name="location_id" id="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
                <option value="">Select location</option>
                    <option value="aaaa">aaaa</option>
                    <option value="aaaa">aaaa</option>
                    <option value="aaaa">aaaa</option>
            </select>
            <button type="submit" class="btn w-100">Continue</button>
        </form>
    </div>
</div>