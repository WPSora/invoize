import{S as Ye,b as Ge,s as He,T as X,U as Z,m as S,J as b,e as d,K as k,p as V,r as c,q as Q,t as $,W as z,h as g,L as w,v as W,be as Me,b6 as ce,bx as Je,aQ as Ke,ag as qe,bj as We,Q as G,X as we,Y as he,y as ie,j as y,k as I,d as j,u as Ve,F as J,g as H,O as re,f as O,C as Xe,aZ as Ze,G as D,H as oe,$ as ze,ao as ye,ap as xe}from"./settings-store-9c8686e2.js";import{R as et,A as tt,a as nt,b as lt,c as st,d as rt,e as ot}from"./index-6c7ffc9c.js";import{T as at,a as ft,b as ut,c as Qe,d as le,e as se}from"./table-row-6a78e972.js";import{T as $t}from"./table-caption-8b655ee4.js";import{R as ct,D as it,S as pt}from"./skeleton-edf9cf6d.js";import{a as _t}from"./card-content-dcfe47df.js";import{C as mt}from"./card-footer-e2822a57.js";import{I as Ne}from"./input-96b35056.js";import{L as pe}from"./label-d084e2b4.js";import{S as dt,a as gt,b as bt,V as kt,c as wt}from"./index-0098572b.js";import{S as ht}from"./separator-f399de31.js";import{i as _e}from"./emptyDataHelper-b583dd0c.js";import{T as vt,a as Bt,M as ve}from"./MiniStar-f74aeb3e.js";import{D as Pt}from"./DeleteButton-149aa44c.js";import{E as St}from"./EditButton-9c11dbce.js";import{U as Dt}from"./UpgradeToProButton-c14d5990.js";import{M as Tt}from"./MultilineText-f439d05e.js";import{D as Ct}from"./dialog-header-2ee0deeb.js";import{D as At}from"./dialog-description-131dd9ea.js";import{P as Ot}from"./plus-66ca668e.js";import{C as Mt}from"./check-f079c4b4.js";function Le(o,e,n){const t=o.slice();return t[34]=e[n],t}function Ee(o,e,n){const t=o.slice();return t[40]=e[n],t[42]=n,t}function Ie(o,e,n){const t=o.slice();return t[37]=e[n],t}function qt(o){let e,n,t,l;return e=new _t({props:{class:"md:px-6 md:pb-6 p-0",$$slots:{default:[nn]},$$scope:{ctx:o}}}),t=new mt({props:{class:"flex mb-2",$$slots:{default:[on]},$$scope:{ctx:o}}}),{c(){b(e.$$.fragment),n=S(),b(t.$$.fragment)},m(s,r){k(e,s,r),d(s,n,r),k(t,s,r),l=!0},p(s,r){const a={};r[0]&104|r[1]&4096&&(a.$$scope={dirty:r,ctx:s}),e.$set(a);const f={};r[0]&448|r[1]&4096&&(f.$$scope={dirty:r,ctx:s}),t.$set(f)},i(s){l||($(e.$$.fragment,s),$(t.$$.fragment,s),l=!0)},o(s){c(e.$$.fragment,s),c(t.$$.fragment,s),l=!1},d(s){s&&g(n),w(e,s),w(t,s)}}}function Nt(o){let e,n,t=y(o[6]),l=[];for(let r=0;r<t.length;r+=1)l[r]=Ue(Ie(o,t,r));const s=r=>c(l[r],1,1,()=>{l[r]=null});return{c(){e=I("div");for(let r=0;r<l.length;r+=1)l[r].c();j(e,"class","mt-5 mb-10 mx-8")},m(r,a){d(r,e,a);for(let f=0;f<l.length;f+=1)l[f]&&l[f].m(e,null);n=!0},p(r,a){if(a[0]&64){t=y(r[6]);let f;for(f=0;f<t.length;f+=1){const u=Ie(r,t,f);l[f]?(l[f].p(u,a),$(l[f],1)):(l[f]=Ue(),l[f].c(),$(l[f],1),l[f].m(e,null))}for(V(),f=t.length;f<l.length;f+=1)s(f);Q()}},i(r){if(!n){for(let a=0;a<t.length;a+=1)$(l[a]);n=!0}},o(r){l=l.filter(Boolean);for(let a=0;a<l.length;a+=1)c(l[a]);n=!1},d(r){r&&g(e),Ve(l,r)}}}function Lt(o){let e,n,t;return n=new at({props:{class:"w-full bg-white rounded-lg md:text-sm text-xs",$$slots:{default:[tn]},$$scope:{ctx:o}}}),{c(){e=I("div"),b(n.$$.fragment),j(e,"class","bg-secondary p-4 rounded-lg")},m(l,s){d(l,e,s),k(n,e,null),t=!0},p(l,s){const r={};s[0]&104|s[1]&4096&&(r.$$scope={dirty:s,ctx:l}),n.$set(r)},i(l){t||($(n.$$.fragment,l),t=!0)},o(l){c(n.$$.fragment,l),t=!1},d(l){l&&g(e),w(n)}}}function Et(o){let e,n,t,l;return e=new ht({}),{c(){b(e.$$.fragment),n=S(),t=I("div"),t.textContent="You have no bank account saved.",j(t,"class","italic text-sm text-muted-foreground mt-4 mb-8")},m(s,r){k(e,s,r),d(s,n,r),d(s,t,r),l=!0},p:H,i(s){l||($(e.$$.fragment,s),l=!0)},o(s){c(e.$$.fragment,s),l=!1},d(s){s&&(g(n),g(t)),w(e,s)}}}function It(o){let e;return{c(){e=D("List of banks used on your invoices.")},m(n,t){d(n,e,t)},d(n){n&&g(e)}}}function Rt(o){let e;return{c(){e=D("Name")},m(n,t){d(n,e,t)},d(n){n&&g(e)}}}function Ft(o){let e;return{c(){e=D("Type")},m(n,t){d(n,e,t)},d(n){n&&g(e)}}}function Ut(o){let e;return{c(){e=D("Currency")},m(n,t){d(n,e,t)},d(n){n&&g(e)}}}function jt(o){let e;return{c(){e=D("Detail")},m(n,t){d(n,e,t)},d(n){n&&g(e)}}}function Vt(o){let e;return{c(){e=D("Actions")},m(n,t){d(n,e,t)},d(n){n&&g(e)}}}function Qt(o){let e,n,t,l,s,r,a,f,u,p;return e=new le({props:{class:"text-center",$$slots:{default:[Rt]},$$scope:{ctx:o}}}),t=new le({props:{class:"text-center",$$slots:{default:[Ft]},$$scope:{ctx:o}}}),s=new le({props:{class:"text-center",$$slots:{default:[Ut]},$$scope:{ctx:o}}}),a=new le({props:{class:"text-center",$$slots:{default:[jt]},$$scope:{ctx:o}}}),u=new le({props:{class:"text-center",$$slots:{default:[Vt]},$$scope:{ctx:o}}}),{c(){b(e.$$.fragment),n=S(),b(t.$$.fragment),l=S(),b(s.$$.fragment),r=S(),b(a.$$.fragment),f=S(),b(u.$$.fragment)},m(_,i){k(e,_,i),d(_,n,i),k(t,_,i),d(_,l,i),k(s,_,i),d(_,r,i),k(a,_,i),d(_,f,i),k(u,_,i),p=!0},p(_,i){const P={};i[1]&4096&&(P.$$scope={dirty:i,ctx:_}),e.$set(P);const m={};i[1]&4096&&(m.$$scope={dirty:i,ctx:_}),t.$set(m);const v={};i[1]&4096&&(v.$$scope={dirty:i,ctx:_}),s.$set(v);const C={};i[1]&4096&&(C.$$scope={dirty:i,ctx:_}),a.$set(C);const A={};i[1]&4096&&(A.$$scope={dirty:i,ctx:_}),u.$set(A)},i(_){p||($(e.$$.fragment,_),$(t.$$.fragment,_),$(s.$$.fragment,_),$(a.$$.fragment,_),$(u.$$.fragment,_),p=!0)},o(_){c(e.$$.fragment,_),c(t.$$.fragment,_),c(s.$$.fragment,_),c(a.$$.fragment,_),c(u.$$.fragment,_),p=!1},d(_){_&&(g(n),g(l),g(r),g(f)),w(e,_),w(t,_),w(s,_),w(a,_),w(u,_)}}}function Yt(o){let e,n;return e=new Qe({props:{$$slots:{default:[Qt]},$$scope:{ctx:o}}}),{c(){b(e.$$.fragment)},m(t,l){k(e,t,l),n=!0},p(t,l){const s={};l[1]&4096&&(s.$$scope={dirty:l,ctx:t}),e.$set(s)},i(t){n||($(e.$$.fragment,t),n=!0)},o(t){c(e.$$.fragment,t),n=!1},d(t){w(e,t)}}}function Re(o){let e=[],n=new Map,t,l,s=y(o[6]);const r=a=>a[42];for(let a=0;a<s.length;a+=1){let f=Ee(o,s,a),u=r(f);n.set(u,e[a]=Fe(u,f))}return{c(){for(let a=0;a<e.length;a+=1)e[a].c();t=J()},m(a,f){for(let u=0;u<e.length;u+=1)e[u]&&e[u].m(a,f);d(a,t,f),l=!0},p(a,f){f[0]&143464&&(s=y(a[6]),V(),e=ye(e,f,r,1,a,s,n,t.parentNode,xe,Fe,t,Ee),Q())},i(a){if(!l){for(let f=0;f<s.length;f+=1)$(e[f]);l=!0}},o(a){for(let f=0;f<e.length;f+=1)c(e[f]);l=!1},d(a){a&&g(t);for(let f=0;f<e.length;f+=1)e[f].d(a)}}}function Gt(o){let e=o[40].name+"",n;return{c(){n=D(e)},m(t,l){d(t,n,l)},p(t,l){l[0]&64&&e!==(e=t[40].name+"")&&oe(n,e)},d(t){t&&g(n)}}}function Ht(o){let e=(o[40].type?o[40].type:"-")+"",n;return{c(){n=D(e)},m(t,l){d(t,n,l)},p(t,l){l[0]&64&&e!==(e=(t[40].type?t[40].type:"-")+"")&&oe(n,e)},d(t){t&&g(n)}}}function Jt(o){var t;let e=((t=o[40].currency)==null?void 0:t.name)+"",n;return{c(){n=D(e)},m(l,s){d(l,n,s)},p(l,s){var r;s[0]&64&&e!==(e=((r=l[40].currency)==null?void 0:r.name)+"")&&oe(n,e)},d(l){l&&g(n)}}}function Kt(o){let e,n;return e=new Tt({props:{text:o[40].detail}}),{c(){b(e.$$.fragment)},m(t,l){k(e,t,l),n=!0},p(t,l){const s={};l[0]&64&&(s.text=t[40].detail),e.$set(s)},i(t){n||($(e.$$.fragment,t),n=!0)},o(t){c(e.$$.fragment,t),n=!1},d(t){w(e,t)}}}function Wt(o){let e,n;function t(){return o[18](o[40])}return e=new re({props:{variant:"outline",class:"text-black text-xs",disabled:o[3],$$slots:{default:[Zt]},$$scope:{ctx:o}}}),e.$on("click",t),{c(){b(e.$$.fragment)},m(l,s){k(e,l,s),n=!0},p(l,s){o=l;const r={};s[0]&8&&(r.disabled=o[3]),s[1]&4096&&(r.$$scope={dirty:s,ctx:o}),e.$set(r)},i(l){n||($(e.$$.fragment,l),n=!0)},o(l){c(e.$$.fragment,l),n=!1},d(l){w(e,l)}}}function Xt(o){let e,n;return e=new re({props:{variant:"outline",class:"text-green-600 text-xs",disabled:!0,$$slots:{default:[zt]},$$scope:{ctx:o}}}),{c(){b(e.$$.fragment)},m(t,l){k(e,t,l),n=!0},p(t,l){const s={};l[1]&4096&&(s.$$scope={dirty:l,ctx:t}),e.$set(s)},i(t){n||($(e.$$.fragment,t),n=!0)},o(t){c(e.$$.fragment,t),n=!1},d(t){w(e,t)}}}function Zt(o){let e;return{c(){e=D("Set as default")},m(n,t){d(n,e,t)},d(n){n&&g(e)}}}function zt(o){let e,n,t;return e=new Mt({props:{class:"h-4 w-4 mr-1"}}),{c(){b(e.$$.fragment),n=D(`
                          Default`)},m(l,s){k(e,l,s),d(l,n,s),t=!0},p:H,i(l){t||($(e.$$.fragment,l),t=!0)},o(l){c(e.$$.fragment,l),t=!1},d(l){l&&g(n),w(e,l)}}}function yt(o){let e,n,t,l,s,r,a,f;const u=[Xt,Wt],p=[];function _(m,v){return m[40].id===m[5]?0:1}n=_(o),t=p[n]=u[n](o);function i(){return o[19](o[42])}s=new St({}),s.$on("click",i);function P(){return o[20](o[42])}return a=new Pt({}),a.$on("click",P),{c(){e=I("div"),t.c(),l=S(),b(s.$$.fragment),r=S(),b(a.$$.fragment),j(e,"class","flex justify-end space-x-1")},m(m,v){d(m,e,v),p[n].m(e,null),O(e,l),k(s,e,null),O(e,r),k(a,e,null),f=!0},p(m,v){o=m;let C=n;n=_(o),n===C?p[n].p(o,v):(V(),c(p[C],1,1,()=>{p[C]=null}),Q(),t=p[n],t?t.p(o,v):(t=p[n]=u[n](o),t.c()),$(t,1),t.m(e,l))},i(m){f||($(t),$(s.$$.fragment,m),$(a.$$.fragment,m),f=!0)},o(m){c(t),c(s.$$.fragment,m),c(a.$$.fragment,m),f=!1},d(m){m&&g(e),p[n].d(),w(s),w(a)}}}function xt(o){let e,n,t,l,s,r,a,f,u,p,_;return e=new se({props:{class:"font-medium truncate text-center",$$slots:{default:[Gt]},$$scope:{ctx:o}}}),t=new se({props:{class:"text-center",$$slots:{default:[Ht]},$$scope:{ctx:o}}}),s=new se({props:{class:"text-center",$$slots:{default:[Jt]},$$scope:{ctx:o}}}),a=new se({props:{class:"text-start",$$slots:{default:[Kt]},$$scope:{ctx:o}}}),u=new se({props:{$$slots:{default:[yt]},$$scope:{ctx:o}}}),{c(){b(e.$$.fragment),n=S(),b(t.$$.fragment),l=S(),b(s.$$.fragment),r=S(),b(a.$$.fragment),f=S(),b(u.$$.fragment),p=S()},m(i,P){k(e,i,P),d(i,n,P),k(t,i,P),d(i,l,P),k(s,i,P),d(i,r,P),k(a,i,P),d(i,f,P),k(u,i,P),d(i,p,P),_=!0},p(i,P){const m={};P[0]&64|P[1]&4096&&(m.$$scope={dirty:P,ctx:i}),e.$set(m);const v={};P[0]&64|P[1]&4096&&(v.$$scope={dirty:P,ctx:i}),t.$set(v);const C={};P[0]&64|P[1]&4096&&(C.$$scope={dirty:P,ctx:i}),s.$set(C);const A={};P[0]&64|P[1]&4096&&(A.$$scope={dirty:P,ctx:i}),a.$set(A);const M={};P[0]&104|P[1]&4096&&(M.$$scope={dirty:P,ctx:i}),u.$set(M)},i(i){_||($(e.$$.fragment,i),$(t.$$.fragment,i),$(s.$$.fragment,i),$(a.$$.fragment,i),$(u.$$.fragment,i),_=!0)},o(i){c(e.$$.fragment,i),c(t.$$.fragment,i),c(s.$$.fragment,i),c(a.$$.fragment,i),c(u.$$.fragment,i),_=!1},d(i){i&&(g(n),g(l),g(r),g(f),g(p)),w(e,i),w(t,i),w(s,i),w(a,i),w(u,i)}}}function Fe(o,e){let n,t,l;return t=new Qe({props:{class:"border-b-0",$$slots:{default:[xt]},$$scope:{ctx:e}}}),{key:o,first:null,c(){n=J(),b(t.$$.fragment),this.first=n},m(s,r){d(s,n,r),k(t,s,r),l=!0},p(s,r){e=s;const a={};r[0]&104|r[1]&4096&&(a.$$scope={dirty:r,ctx:e}),t.$set(a)},i(s){l||($(t.$$.fragment,s),l=!0)},o(s){c(t.$$.fragment,s),l=!1},d(s){s&&g(n),w(t,s)}}}function en(o){let e=!_e(o[6]),n,t,l=e&&Re(o);return{c(){l&&l.c(),n=J()},m(s,r){l&&l.m(s,r),d(s,n,r),t=!0},p(s,r){r[0]&64&&(e=!_e(s[6])),e?l?(l.p(s,r),r[0]&64&&$(l,1)):(l=Re(s),l.c(),$(l,1),l.m(n.parentNode,n)):l&&(V(),c(l,1,1,()=>{l=null}),Q())},i(s){t||($(l),t=!0)},o(s){c(l),t=!1},d(s){s&&g(n),l&&l.d(s)}}}function tn(o){let e,n,t,l,s,r;return e=new $t({props:{$$slots:{default:[It]},$$scope:{ctx:o}}}),t=new ft({props:{class:"border-b-secondary border-b-4",$$slots:{default:[Yt]},$$scope:{ctx:o}}}),s=new ut({props:{$$slots:{default:[en]},$$scope:{ctx:o}}}),{c(){b(e.$$.fragment),n=S(),b(t.$$.fragment),l=S(),b(s.$$.fragment)},m(a,f){k(e,a,f),d(a,n,f),k(t,a,f),d(a,l,f),k(s,a,f),r=!0},p(a,f){const u={};f[1]&4096&&(u.$$scope={dirty:f,ctx:a}),e.$set(u);const p={};f[1]&4096&&(p.$$scope={dirty:f,ctx:a}),t.$set(p);const _={};f[0]&104|f[1]&4096&&(_.$$scope={dirty:f,ctx:a}),s.$set(_)},i(a){r||($(e.$$.fragment,a),$(t.$$.fragment,a),$(s.$$.fragment,a),r=!0)},o(a){c(e.$$.fragment,a),c(t.$$.fragment,a),c(s.$$.fragment,a),r=!1},d(a){a&&(g(n),g(l)),w(e,a),w(t,a),w(s,a)}}}function nn(o){let e,n,t,l,s;const r=[Et,Lt],a=[];function f(u,p){return p[0]&64&&(e=null),e==null&&(e=!!(_e(u[6])||u[6].length===0)),e?0:1}return n=f(o,[-1,-1]),t=a[n]=r[n](o),{c(){t.c(),l=J()},m(u,p){a[n].m(u,p),d(u,l,p),s=!0},p(u,p){let _=n;n=f(u,p),n===_?a[n].p(u,p):(V(),c(a[_],1,1,()=>{a[_]=null}),Q(),t=a[n],t?t.p(u,p):(t=a[n]=r[n](u),t.c()),$(t,1),t.m(l.parentNode,l))},i(u){s||($(t),s=!0)},o(u){c(t),s=!1},d(u){u&&g(l),a[n].d(u)}}}function ln(o){let e,n;return e=new re({props:{$$slots:{default:[rn]},$$scope:{ctx:o}}}),e.$on("click",o[11]),{c(){b(e.$$.fragment)},m(t,l){k(e,t,l),n=!0},p(t,l){const s={};l[1]&4096&&(s.$$scope={dirty:l,ctx:t}),e.$set(s)},i(t){n||($(e.$$.fragment,t),n=!0)},o(t){c(e.$$.fragment,t),n=!1},d(t){w(e,t)}}}function sn(o){let e,n,t;function l(r){o[21](r)}let s={customText:"Upgrade to Pro to Add More Bank Accounts"};return o[8]!==void 0&&(s.isProPopupOpen=o[8]),e=new Dt({props:s}),X.push(()=>Z(e,"isProPopupOpen",l)),{c(){b(e.$$.fragment)},m(r,a){k(e,r,a),t=!0},p(r,a){const f={};!n&&a[0]&256&&(n=!0,f.isProPopupOpen=r[8],z(()=>n=!1)),e.$set(f)},i(r){t||($(e.$$.fragment,r),t=!0)},o(r){c(e.$$.fragment,r),t=!1},d(r){w(e,r)}}}function rn(o){let e,n,t;return e=new Ot({props:{class:"h-4"}}),{c(){b(e.$$.fragment),n=D(`
        Bank Account`)},m(l,s){k(e,l,s),d(l,n,s),t=!0},p:H,i(l){t||($(e.$$.fragment,l),t=!0)},o(l){c(e.$$.fragment,l),t=!1},d(l){l&&g(n),w(e,l)}}}function on(o){let e,n,t,l;const s=[sn,ln],r=[];function a(f,u){return!f[7]&&f[6].length>=3?0:1}return e=a(o),n=r[e]=s[e](o),{c(){n.c(),t=J()},m(f,u){r[e].m(f,u),d(f,t,u),l=!0},p(f,u){let p=e;e=a(f),e===p?r[e].p(f,u):(V(),c(r[p],1,1,()=>{r[p]=null}),Q(),n=r[e],n?n.p(f,u):(n=r[e]=s[e](f),n.c()),$(n,1),n.m(t.parentNode,t))},i(f){l||($(n),l=!0)},o(f){c(n),l=!1},d(f){f&&g(t),r[e].d(f)}}}function Ue(o){let e,n;return e=new pt({props:{class:"h-[20px] my-8 rounded-full"}}),{c(){b(e.$$.fragment)},m(t,l){k(e,t,l),n=!0},p:H,i(t){n||($(e.$$.fragment,t),n=!0)},o(t){c(e.$$.fragment,t),n=!1},d(t){w(e,t)}}}function an(o){let e;return{c(){e=D("Payment Account")},m(n,t){d(n,e,t)},d(n){n&&g(e)}}}function fn(o){let e;return{c(){e=D("Manage your bank account for receiving payment.")},m(n,t){d(n,e,t)},d(n){n&&g(e)}}}function un(o){let e,n,t,l;return e=new Bt({props:{$$slots:{default:[an]},$$scope:{ctx:o}}}),t=new At({props:{$$slots:{default:[fn]},$$scope:{ctx:o}}}),{c(){b(e.$$.fragment),n=S(),b(t.$$.fragment)},m(s,r){k(e,s,r),d(s,n,r),k(t,s,r),l=!0},p(s,r){const a={};r[1]&4096&&(a.$$scope={dirty:r,ctx:s}),e.$set(a);const f={};r[1]&4096&&(f.$$scope={dirty:r,ctx:s}),t.$set(f)},i(s){l||($(e.$$.fragment,s),$(t.$$.fragment,s),l=!0)},o(s){c(e.$$.fragment,s),c(t.$$.fragment,s),l=!1},d(s){s&&g(n),w(e,s),w(t,s)}}}function $n(o){let e,n,t;return n=new ve({}),{c(){e=D("Name "),b(n.$$.fragment)},m(l,s){d(l,e,s),k(n,l,s),t=!0},i(l){t||($(n.$$.fragment,l),t=!0)},o(l){c(n.$$.fragment,l),t=!1},d(l){l&&g(e),w(n,l)}}}function cn(o){let e;return{c(){e=D("Type")},m(n,t){d(n,e,t)},d(n){n&&g(e)}}}function pn(o){let e,n,t;return n=new ve({}),{c(){e=D("Currency "),b(n.$$.fragment)},m(l,s){d(l,e,s),k(n,l,s),t=!0},i(l){t||($(n.$$.fragment,l),t=!0)},o(l){c(n.$$.fragment,l),t=!1},d(l){l&&g(e),w(n,l)}}}function _n(o){let e,n;return e=new kt({props:{placeholder:"Select currency"}}),{c(){b(e.$$.fragment)},m(t,l){k(e,t,l),n=!0},p:H,i(t){n||($(e.$$.fragment,t),n=!0)},o(t){c(e.$$.fragment,t),n=!1},d(t){w(e,t)}}}function mn(o){let e=o[34].name+"",n;return{c(){n=D(e)},m(t,l){d(t,n,l)},p(t,l){l[0]&512&&e!==(e=t[34].name+"")&&oe(n,e)},d(t){t&&g(n)}}}function je(o){let e,n;return e=new wt({props:{value:o[34],label:o[34].name,$$slots:{default:[mn]},$$scope:{ctx:o}}}),{c(){b(e.$$.fragment)},m(t,l){k(e,t,l),n=!0},p(t,l){const s={};l[0]&512&&(s.value=t[34]),l[0]&512&&(s.label=t[34].name),l[0]&512|l[1]&4096&&(s.$$scope={dirty:l,ctx:t}),e.$set(s)},i(t){n||($(e.$$.fragment,t),n=!0)},o(t){c(e.$$.fragment,t),n=!1},d(t){w(e,t)}}}function dn(o){let e,n,t=y(o[9]),l=[];for(let r=0;r<t.length;r+=1)l[r]=je(Le(o,t,r));const s=r=>c(l[r],1,1,()=>{l[r]=null});return{c(){for(let r=0;r<l.length;r+=1)l[r].c();e=J()},m(r,a){for(let f=0;f<l.length;f+=1)l[f]&&l[f].m(r,a);d(r,e,a),n=!0},p(r,a){if(a[0]&512){t=y(r[9]);let f;for(f=0;f<t.length;f+=1){const u=Le(r,t,f);l[f]?(l[f].p(u,a),$(l[f],1)):(l[f]=je(u),l[f].c(),$(l[f],1),l[f].m(e.parentNode,e))}for(V(),f=t.length;f<l.length;f+=1)s(f);Q()}},i(r){if(!n){for(let a=0;a<t.length;a+=1)$(l[a]);n=!0}},o(r){l=l.filter(Boolean);for(let a=0;a<l.length;a+=1)c(l[a]);n=!1},d(r){r&&g(e),Ve(l,r)}}}function gn(o){let e,n,t,l;return e=new gt({props:{id:"currency",$$slots:{default:[_n]},$$scope:{ctx:o}}}),t=new bt({props:{class:"max-h-60 overflow-y-auto",$$slots:{default:[dn]},$$scope:{ctx:o}}}),{c(){b(e.$$.fragment),n=S(),b(t.$$.fragment)},m(s,r){k(e,s,r),d(s,n,r),k(t,s,r),l=!0},p(s,r){const a={};r[1]&4096&&(a.$$scope={dirty:r,ctx:s}),e.$set(a);const f={};r[0]&512|r[1]&4096&&(f.$$scope={dirty:r,ctx:s}),t.$set(f)},i(s){l||($(e.$$.fragment,s),$(t.$$.fragment,s),l=!0)},o(s){c(e.$$.fragment,s),c(t.$$.fragment,s),l=!1},d(s){s&&g(n),w(e,s),w(t,s)}}}function bn(o){let e,n,t;return n=new ve({}),{c(){e=D("Detail "),b(n.$$.fragment)},m(l,s){d(l,e,s),k(n,l,s),t=!0},i(l){t||($(n.$$.fragment,l),t=!0)},o(l){c(n.$$.fragment,l),t=!1},d(l){l&&g(e),w(n,l)}}}function kn(o){let e;return{c(){e=D("Save")},m(n,t){d(n,e,t)},i:H,o:H,d(n){n&&g(e)}}}function wn(o){let e,n,t;return e=new ze({props:{class:"mr-2 w-4 h-4 animate-spin"}}),{c(){b(e.$$.fragment),n=D(`
          Saving`)},m(l,s){k(e,l,s),d(l,n,s),t=!0},i(l){t||($(e.$$.fragment,l),t=!0)},o(l){c(e.$$.fragment,l),t=!1},d(l){l&&g(n),w(e,l)}}}function hn(o){let e,n,t,l;const s=[wn,kn],r=[];function a(f,u){return f[2]?0:1}return e=a(o),n=r[e]=s[e](o),{c(){n.c(),t=J()},m(f,u){r[e].m(f,u),d(f,t,u),l=!0},p(f,u){let p=e;e=a(f),e!==p&&(V(),c(r[p],1,1,()=>{r[p]=null}),Q(),n=r[e],n||(n=r[e]=s[e](f),n.c()),$(n,1),n.m(t.parentNode,t))},i(f){l||($(n),l=!0)},o(f){c(n),l=!1},d(f){f&&g(t),r[e].d(f)}}}function vn(o){let e,n,t,l,s,r,a,f,u,p,_,i,P,m,v,C,A,M,L,x,ee,R,F,ae,E,te,Y,U,K,ne,fe;e=new Ct({props:{$$slots:{default:[un]},$$scope:{ctx:o}}}),s=new pe({props:{for:"name",$$slots:{default:[$n]},$$scope:{ctx:o}}});function me(B){o[22](B)}let ue={type:"text",id:"name",required:!0,placeholder:"Bank name"};o[4].name!==void 0&&(ue.value=o[4].name),a=new Ne({props:ue}),X.push(()=>Z(a,"value",me)),_=new pe({props:{for:"type",$$slots:{default:[cn]},$$scope:{ctx:o}}});function de(B){o[23](B)}let $e={type:"text",id:"type",placeholder:"Bank account type"};o[4].type!==void 0&&($e.value=o[4].type),P=new Ne({props:$e}),X.push(()=>Z(P,"value",de)),A=new pe({props:{for:"currency",$$slots:{default:[pn]},$$scope:{ctx:o}}});function ge(B){o[24](B)}let h={required:!0,$$slots:{default:[gn]},$$scope:{ctx:o}};o[4].currency!==void 0&&(h.selected=o[4].currency),L=new dt({props:h}),X.push(()=>Z(L,"selected",ge)),F=new pe({props:{for:"detail",$$slots:{default:[bn]},$$scope:{ctx:o}}});function q(B){o[25](B)}let N={id:"detail",required:!0,rows:5,placeholder:"Bank account number and other information here"};return o[4].detail!==void 0&&(N.value=o[4].detail),E=new vt({props:N}),X.push(()=>Z(E,"value",q)),U=new re({props:{disabled:o[2],type:"submit",$$slots:{default:[hn]},$$scope:{ctx:o}}}),{c(){b(e.$$.fragment),n=S(),t=I("form"),l=I("div"),b(s.$$.fragment),r=S(),b(a.$$.fragment),u=S(),p=I("div"),b(_.$$.fragment),i=S(),b(P.$$.fragment),v=S(),C=I("div"),b(A.$$.fragment),M=S(),b(L.$$.fragment),ee=S(),R=I("div"),b(F.$$.fragment),ae=S(),b(E.$$.fragment),Y=S(),b(U.$$.fragment),j(l,"class","space-y-2"),j(p,"class","space-y-2"),j(C,"class","space-y-2"),j(R,"class","space-y-2"),j(t,"class","space-y-4")},m(B,T){k(e,B,T),d(B,n,T),d(B,t,T),O(t,l),k(s,l,null),O(l,r),k(a,l,null),O(t,u),O(t,p),k(_,p,null),O(p,i),k(P,p,null),O(t,v),O(t,C),k(A,C,null),O(C,M),k(L,C,null),O(t,ee),O(t,R),k(F,R,null),O(R,ae),k(E,R,null),O(t,Y),k(U,t,null),K=!0,ne||(fe=Xe(t,"submit",Ze(o[15])),ne=!0)},p(B,T){const Be={};T[1]&4096&&(Be.$$scope={dirty:T,ctx:B}),e.$set(Be);const Pe={};T[1]&4096&&(Pe.$$scope={dirty:T,ctx:B}),s.$set(Pe);const Se={};!f&&T[0]&16&&(f=!0,Se.value=B[4].name,z(()=>f=!1)),a.$set(Se);const De={};T[1]&4096&&(De.$$scope={dirty:T,ctx:B}),_.$set(De);const Te={};!m&&T[0]&16&&(m=!0,Te.value=B[4].type,z(()=>m=!1)),P.$set(Te);const Ce={};T[1]&4096&&(Ce.$$scope={dirty:T,ctx:B}),A.$set(Ce);const be={};T[0]&512|T[1]&4096&&(be.$$scope={dirty:T,ctx:B}),!x&&T[0]&16&&(x=!0,be.selected=B[4].currency,z(()=>x=!1)),L.$set(be);const Ae={};T[1]&4096&&(Ae.$$scope={dirty:T,ctx:B}),F.$set(Ae);const Oe={};!te&&T[0]&16&&(te=!0,Oe.value=B[4].detail,z(()=>te=!1)),E.$set(Oe);const ke={};T[0]&4&&(ke.disabled=B[2]),T[0]&4|T[1]&4096&&(ke.$$scope={dirty:T,ctx:B}),U.$set(ke)},i(B){K||($(e.$$.fragment,B),$(s.$$.fragment,B),$(a.$$.fragment,B),$(_.$$.fragment,B),$(P.$$.fragment,B),$(A.$$.fragment,B),$(L.$$.fragment,B),$(F.$$.fragment,B),$(E.$$.fragment,B),$(U.$$.fragment,B),K=!0)},o(B){c(e.$$.fragment,B),c(s.$$.fragment,B),c(a.$$.fragment,B),c(_.$$.fragment,B),c(P.$$.fragment,B),c(A.$$.fragment,B),c(L.$$.fragment,B),c(F.$$.fragment,B),c(E.$$.fragment,B),c(U.$$.fragment,B),K=!1},d(B){B&&(g(n),g(t)),w(e,B),w(s),w(a),w(_),w(P),w(A),w(L),w(F),w(E),w(U),ne=!1,fe()}}}function Bn(o){let e,n;return e=new it({props:{class:"sm:max-w-lg",$$slots:{default:[vn]},$$scope:{ctx:o}}}),{c(){b(e.$$.fragment)},m(t,l){k(e,t,l),n=!0},p(t,l){const s={};l[0]&532|l[1]&4096&&(s.$$scope={dirty:l,ctx:t}),e.$set(s)},i(t){n||($(e.$$.fragment,t),n=!0)},o(t){c(e.$$.fragment,t),n=!1},d(t){w(e,t)}}}function Pn(o){let e;return{c(){e=D("Are you absolutely sure?")},m(n,t){d(n,e,t)},d(n){n&&g(e)}}}function Sn(o){let e,n=" ",t,l,s,r=o[4].name+"",a,f;return{c(){e=D("This action cannot be undone. This will permanently delete the"),t=D(n),l=S(),s=I("b"),a=D(r),f=D(" account.")},m(u,p){d(u,e,p),d(u,t,p),d(u,l,p),d(u,s,p),O(s,a),d(u,f,p)},p(u,p){p[0]&16&&r!==(r=u[4].name+"")&&oe(a,r)},d(u){u&&(g(e),g(t),g(l),g(s),g(f))}}}function Dn(o){let e,n,t,l;return e=new st({props:{$$slots:{default:[Pn]},$$scope:{ctx:o}}}),t=new rt({props:{$$slots:{default:[Sn]},$$scope:{ctx:o}}}),{c(){b(e.$$.fragment),n=S(),b(t.$$.fragment)},m(s,r){k(e,s,r),d(s,n,r),k(t,s,r),l=!0},p(s,r){const a={};r[1]&4096&&(a.$$scope={dirty:r,ctx:s}),e.$set(a);const f={};r[0]&16|r[1]&4096&&(f.$$scope={dirty:r,ctx:s}),t.$set(f)},i(s){l||($(e.$$.fragment,s),$(t.$$.fragment,s),l=!0)},o(s){c(e.$$.fragment,s),c(t.$$.fragment,s),l=!1},d(s){s&&g(n),w(e,s),w(t,s)}}}function Tn(o){let e;return{c(){e=D("Cancel")},m(n,t){d(n,e,t)},d(n){n&&g(e)}}}function Cn(o){let e;return{c(){e=D("Delete")},m(n,t){d(n,e,t)},d(n){n&&g(e)}}}function An(o){let e,n,t,l;return e=new ot({props:{$$slots:{default:[Tn]},$$scope:{ctx:o}}}),t=new re({props:{variant:"destructive",$$slots:{default:[Cn]},$$scope:{ctx:o}}}),t.$on("click",o[16]),{c(){b(e.$$.fragment),n=S(),b(t.$$.fragment)},m(s,r){k(e,s,r),d(s,n,r),k(t,s,r),l=!0},p(s,r){const a={};r[1]&4096&&(a.$$scope={dirty:r,ctx:s}),e.$set(a);const f={};r[1]&4096&&(f.$$scope={dirty:r,ctx:s}),t.$set(f)},i(s){l||($(e.$$.fragment,s),$(t.$$.fragment,s),l=!0)},o(s){c(e.$$.fragment,s),c(t.$$.fragment,s),l=!1},d(s){s&&g(n),w(e,s),w(t,s)}}}function On(o){let e,n,t,l;return e=new nt({props:{$$slots:{default:[Dn]},$$scope:{ctx:o}}}),t=new lt({props:{$$slots:{default:[An]},$$scope:{ctx:o}}}),{c(){b(e.$$.fragment),n=S(),b(t.$$.fragment)},m(s,r){k(e,s,r),d(s,n,r),k(t,s,r),l=!0},p(s,r){const a={};r[0]&16|r[1]&4096&&(a.$$scope={dirty:r,ctx:s}),e.$set(a);const f={};r[1]&4096&&(f.$$scope={dirty:r,ctx:s}),t.$set(f)},i(s){l||($(e.$$.fragment,s),$(t.$$.fragment,s),l=!0)},o(s){c(e.$$.fragment,s),c(t.$$.fragment,s),l=!1},d(s){s&&g(n),w(e,s),w(t,s)}}}function Mn(o){let e,n;return e=new tt({props:{$$slots:{default:[On]},$$scope:{ctx:o}}}),{c(){b(e.$$.fragment)},m(t,l){k(e,t,l),n=!0},p(t,l){const s={};l[0]&16|l[1]&4096&&(s.$$scope={dirty:l,ctx:t}),e.$set(s)},i(t){n||($(e.$$.fragment,t),n=!0)},o(t){c(e.$$.fragment,t),n=!1},d(t){w(e,t)}}}function qn(o){let e,n,t,l,s,r,a,f;const u=[Nt,qt],p=[];function _(m,v){return m[2]?0:1}e=_(o),n=p[e]=u[e](o);function i(m){o[26](m)}let P={onOpenChange:o[10],$$slots:{default:[Bn]},$$scope:{ctx:o}};return o[1]!==void 0&&(P.open=o[1]),l=new ct({props:P}),X.push(()=>Z(l,"open",i)),a=new et({props:{open:o[0],onOpenChange:o[14],$$slots:{default:[Mn]},$$scope:{ctx:o}}}),{c(){n.c(),t=S(),b(l.$$.fragment),r=S(),b(a.$$.fragment)},m(m,v){p[e].m(m,v),d(m,t,v),k(l,m,v),d(m,r,v),k(a,m,v),f=!0},p(m,v){let C=e;e=_(m),e===C?p[e].p(m,v):(V(),c(p[C],1,1,()=>{p[C]=null}),Q(),n=p[e],n?n.p(m,v):(n=p[e]=u[e](m),n.c()),$(n,1),n.m(t.parentNode,t));const A={};v[0]&532|v[1]&4096&&(A.$$scope={dirty:v,ctx:m}),!s&&v[0]&2&&(s=!0,A.open=m[1],z(()=>s=!1)),l.$set(A);const M={};v[0]&1&&(M.open=m[0]),v[0]&16|v[1]&4096&&(M.$$scope={dirty:v,ctx:m}),a.$set(M)},i(m){f||($(n),$(l.$$.fragment,m),$(a.$$.fragment,m),f=!0)},o(m){c(n),c(l.$$.fragment,m),c(a.$$.fragment,m),f=!1},d(m){m&&(g(t),g(r)),p[e].d(m),w(l,m),w(a,m)}}}function Nn(o,e,n){let t,l,s,r,a,f;W(o,Me,h=>n(5,t=h)),W(o,ce,h=>n(6,l=h)),W(o,Je,h=>n(29,s=h)),W(o,Ke,h=>n(7,r=h)),W(o,qe,h=>n(8,a=h)),W(o,We,h=>n(9,f=h));let u=!1,p=!1,_=!1,i=!1,P=!1,m=0,v={id:0,name:"",type:"",currency:null,detail:""};const C=h=>{_?ie(ce,l[m]=h,l):ie(ce,l=[...l,h],l)},A=h=>{!_e(l)&&l.length===1&&Y(h.id)},M=()=>{n(4,v={id:0,name:"",type:"",currency:{label:"",value:""},detail:""})},L=()=>{M(),x(),n(1,p=!0),_=!1},x=()=>{n(4,v.currency={label:s.name,value:{...s}},v)},ee=h=>{m=h,_=!0;const q={...l[m]};n(4,v=q);const N={label:q.currency.name,value:q.currency};n(4,v.currency=N,v),n(1,p=!0)},R=h=>{n(0,u=!0),m=h},F=()=>{n(0,u=!1)},ae=()=>{var h;return((h=v.currency)==null?void 0:h.label)===""?(G.error("Please select a currency."),!1):!0},E=()=>{if(i||!ae())return;let h={...v};h.currency=h.currency.value,n(2,i=!0),G.loading("Saving..."),we(_?"bank/edit":"bank/add",h,N=>{G.dismiss(),G.success("Setting Saved"),C(N.data.data),A(N.data.data),M(),n(1,p=!1),n(2,i=!1)}).catch(N=>{G.dismiss(),n(2,i=!1),n(1,p=!1),he(N,"Failed to save settings")})},te=()=>{n(2,i=!0);const h=l[m].id;G.promise(we("bank/delete",{id:h}),{loading:"Saving...",success:()=>{const q=l.find((N,B)=>B===m);return ie(ce,l=l.filter((N,B)=>B!==m),l),q.id===t&&l.length>0?Y(l[0].id):q.id===t&&l.length===0&&Y(0),M(),n(1,p=!1),n(2,i=!1),"Setting Saved"},error:q=>(n(2,i=!1),n(1,p=!1),he(q,"Failed to save settings",!1))}),n(0,u=!1)},Y=h=>{n(3,P=!0),G.promise(we("settings/update?tab=payment",{defaultBank:h}),{loading:"Saving...",success:()=>(ie(Me,t=h,t),n(3,P=!1),"Default bank updated"),error:q=>(n(3,P=!1),he(q,"Failed to save default bank",!1))})},U=h=>Y(h.id),K=h=>ee(h),ne=h=>R(h);function fe(h){a=h,qe.set(a)}function me(h){o.$$.not_equal(v.name,h)&&(v.name=h,n(4,v))}function ue(h){o.$$.not_equal(v.type,h)&&(v.type=h,n(4,v))}function de(h){o.$$.not_equal(v.currency,h)&&(v.currency=h,n(4,v))}function $e(h){o.$$.not_equal(v.detail,h)&&(v.detail=h,n(4,v))}function ge(h){p=h,n(1,p)}return[u,p,i,P,v,t,l,r,a,f,M,L,ee,R,F,E,te,Y,U,K,ne,fe,me,ue,de,$e,ge]}class tl extends Ye{constructor(e){super(),Ge(this,e,Nn,qn,He,{},null,[-1,-1])}}export{tl as B};
