App.service("Message", function($timeout) {

    var Message = function() {

        this.is_error = false;
        this.text = "";
        this.is_visible = false;
        this.timer = null;

        this.setText = function(text) {
            this.text = text;
            return this;
        };

        this.isError = function(is_error) {
            this.is_error = is_error;
            return this;
        };

        this.show = function() {

            this.is_visible = true;

            if(this.timer) {
                $timeout.cancel(this.timer);
            }

            this.timer = $timeout(function() {
                this.is_visible = false;
                this.timer = null;
            }.bind(this), 4000);

            return this;
        }

    }

    return Message;

});