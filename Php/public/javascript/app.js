var notFound = {
    template: `    
    <v-layout align-center justify-center column fill-height>
        <div class="text-xs-center">
            <v-icon x-large>mdi-info</v-icon>
        </div>
        <h3 class="headline mb-0 text-xs-center">Ops</h3>
        <div class="text-xs-center">No Record been found</div>
    </v-layout>
`,
}
var appMixin = {
    delimiters: ['${', '}'],
    domStreams: [],
    subscriptions() {


    },
    components: {
        'not-found': notFound,
    },
    data: function () {
        return {

            infoBar: {
                display: false,
                title: '',
                timeout: 2000,
            },
            infoDialog: {
                display: false,
                title: 'success',
            },
            drawer: null,
            menus: [],
            endpoints: [],
            applications: [{
                icon: '',
                label: 'eClaim',
                url: ''
            },
            {
                icon: '',
                label: 'ePR',
                url: ''
            },
            {
                icon: '',
                label: 'sugarCRM',
                url: ''
            },
            {
                icon: '',
                label: 'Outlook',
                url: ''
            },
            {
                icon: '',
                label: 'REDtone Academy',
                url: ''
            },
            {
                icon: '',
                label: 'Flex ESS',
                url: ''
            },
            {
                icon: '',
                label: 'MIS Ticketing',
                url: ''
            }
            ],
            socials: [{
                icon: '',
                label: 'Offical Website',
                url: ''
            },
            {
                icon: '',
                label: 'Facebook',
                url: ''
            },
            {
                icon: '',
                label: 'Linkedin',
                url: ''
            },
            {
                icon: '',
                label: 'Youtube',
                url: ''
            }
            ],
            profile: {
                display: false,
                name: 'Alex Au Yong',
                email: '',
                designation: 'Developer',
                background: '',
                avatar: 'https://randomuser.me/api/portraits/men/85.jpg',
                isAuthed: false,
            },
            system: {
                name: 'Vue Application',
            },

            production: false,
        }
    },
    computed: {
        disableOnSpecific: function () {
            let preventTarget = ['login', 'error']
            return false;
        },

    },
    beforeCreate() {
        this.$http.bind(this);

    },
    async created() {


    },


    beforeMount() {

    },
    mounted() {

    },
    beforeDestroy() { },
    destroyed() {

    },

    //global Filter
    filters: {
        capitalize: function (value) {
            if (!value) return ''
            value = value.toString()
            return value.charAt(0).toUpperCase() + value.slice(1)
        },
        shorten: function (value, length = 20) {
            if (!value) return '',
                value = value.toString();
            return value.substring(0, parseInt(length));
        },
        decimal: function (value, point = 2) {
            return parseFloat(value).toFixed(point);
        },
        date: function (value) {
            if (!value) return ''
            var hms = value.substr(11, value.length).split(':');
            var date = new Date(value)
            var hour = date.getHours();
            var minute = date.getMinutes(); //hms[1];
            var second = date.getSeconds(); //hms[1];
            var mode = hour < 13 ? 'AM' : 'PM'

            minute = minute < 10 ? `0${minute}` : minute;
            second = second < 10 ? `0${second}` : second;

            return `${date.toISOString().substr(0, 10)}  ${hour}:${minute}:${second} ${mode}`
        },
        hour: function (value) {
            let tempDate = new Date(value * 1000);
            return tempDate.toLocaleString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                hour24: true
            });
        },
        /*day convert*/
        day: function (value) {
            const days = {
                1: {
                    abbr: 'Mon',
                    full: 'Monday'
                },
                2: {
                    abbr: 'Tue',
                    full: 'Tuesday'
                },
                3: {
                    abbr: 'Wed',
                    full: 'Wednesday'
                },
                4: {
                    abbr: 'Thr',
                    full: 'Thursday'
                },
                5: {
                    abbr: 'Fri',
                    full: 'Friday'
                },
                6: {
                    abbr: 'Sat',
                    full: 'Saturday'
                },
                7: {
                    abbr: 'Sun',
                    full: 'Sunday'
                },
            }
            let format = 'abbr';
            return days[value][format];
        },

    },
    watch: {

    },
    methods: {

        async registerMENU() {
            let config = {
                url: `./data/menu.json?${new Date().toISOString()}`,
                method: 'get',
                withCredentials: true,
                headers: {
                    'Content-Type': 'application/json',
                },
            }
            let result = await this.$http.request(config);
            if (result.status == 200) this.menus = result.data;
        },
        async registerAPI() {
            let config = {
                url: `./data/endpoint.json?${new Date().toISOString()}`,
                method: 'get',
                withCredentials: true,
                headers: {
                    'Content-Type': 'application/json',
                },
            }
            let result = await this.$http.request(config);
            this.endpoints = result.data;

        },
        async registerPROFILE() {
            let config = {
                url: `${this.getEndPoint('google')}/oAuth/session`,
                method: 'get',
                withCredentials: true,
                xsrfCookieName: 'Json-Token',
                xsrfHeaderName: 'Json-Token',
                headers: {
                    'Content-Type': 'application/json',
                },
            }
            this.$http.request(config).then((resp) => {
                if (resp.status != 200) {
                    this.profile.isAuthed = false;
                    return;
                }
                let result = resp.data;
                this.profile.isAuthed = true;
                Object.keys(this.profile).forEach((prop) => {
                    if (result[prop] != undefined) this.profile[prop] = result[prop];
                })
            })

        },


        getEndPoint(metadata) {
            if (this.endpoints.find((endPoint) => endPoint.metadata == metadata) == undefined) {
                console.error('Invalid metadata!');
                return false;

            }
            let selected = this.endpoints.find((endpoint) => endpoint.metadata == metadata);
            return this.production ? selected.production : selected.development;
        },

        mobileAndTabletCheck() {
            let check = false;
            (function (a) {
                if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i
                    .test(a) ||
                    /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i
                        .test(a.substr(0, 4))) check = true;
            })(navigator.userAgent || navigator.vendor || window.opera);
            return check;
        },
        onTap(e, value) {
            if (typeof window.orientation !== 'undefined') {
                this.appConfig.drawer = false;
                return;
            }
            if (this.mobileAndTabletCheck()) {
                this.appConfig.drawer = false
                return;
            }


        },
        onLogout() {
            window.location = '/api/google/oAuth/logout';

        },
        onRefresh() {
            location.reload();

        },
        onHome() {
        },
        onNavigate(url) {
            if (url == undefined) return;
            if (url.length == 0) return;
            window.location = url;
        },
        getLabel: function (optionName, value) {
            let label = '';
            if (this[optionName] == undefined) return;
            this[optionName].map((option) => {
                if (option.value == value) {
                    label = option.label;
                }
            })
            return label;
        },
        setSystemName(name = '') {
            this.system.name = name;
        },
        setLockStat(stat = true) {
            this.system.lock = stat;

        }
    }


};

let appTheme = {
    theme: {
        themes: {
            light: {
                primary: '#3f51b5',
                secondary: '#b0bec5',
                accent: '#8c9eff',
                error: '#b71c1c',
            },
        },
    },
}