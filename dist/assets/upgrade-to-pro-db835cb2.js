import{S,b as z,s as B,J as m,K as p,t as u,r as i,L as _,m as h,e as g,h as d,j as k,k as C,d as b,p as q,q as I,u as L,O as y,G as v,f as w,g as F}from"./settings-store-9c8686e2.js";import{C as G,a as H}from"./card-content-dcfe47df.js";import{C as J}from"./card-footer-e2822a57.js";import{C as K,a as M}from"./card-title-0b899c1a.js";import{S as O}from"./separator-f399de31.js";import{p as U,C as T}from"./options-0c96a228.js";import{P as A}from"./index-41c590ae.js";function j($,e,n){const t=$.slice();return t[0]=e[n],t}function D($){let e,n,t;return n=new A({}),{c(){e=v(`Upgrade to Invoize
      `),m(n.$$.fragment)},m(o,r){g(o,e,r),p(n,o,r),t=!0},i(o){t||(u(n.$$.fragment,o),t=!0)},o(o){i(n.$$.fragment,o),t=!1},d(o){o&&d(e),_(n,o)}}}function E($){let e,n,t,o;return e=new M({props:{class:"text-primary sm:text-xl text-lg flex justify-center",$$slots:{default:[D]},$$scope:{ctx:$}}}),t=new O({}),{c(){m(e.$$.fragment),n=h(),m(t.$$.fragment)},m(r,a){p(e,r,a),g(r,n,a),p(t,r,a),o=!0},p(r,a){const l={};a&8&&(l.$$scope={dirty:a,ctx:r}),e.$set(l)},i(r){o||(u(e.$$.fragment,r),u(t.$$.fragment,r),o=!0)},o(r){i(e.$$.fragment,r),i(t.$$.fragment,r),o=!1},d(r){r&&d(n),_(e,r),_(t,r)}}}function P($){let e,n,t,o=$[0]+"",r,a,l;return n=new T({props:{class:"h-4 w-4 mr-3 text-primary"}}),{c(){e=C("li"),m(n.$$.fragment),t=h(),r=v(o),a=h(),b(e,"class","flex items-center")},m(s,c){g(s,e,c),p(n,e,null),w(e,t),w(e,r),w(e,a),l=!0},p:F,i(s){l||(u(n.$$.fragment,s),l=!0)},o(s){i(n.$$.fragment,s),l=!1},d(s){s&&d(e),_(n)}}}function N($){let e,n,t,o,r=k(U),a=[];for(let s=0;s<r.length;s+=1)a[s]=P(j($,r,s));const l=s=>i(a[s],1,1,()=>{a[s]=null});return{c(){e=C("div"),e.innerHTML='Upgrade to <span class="text-primary">Invoize Pro</span> now to start enjoying these benefits:',n=h(),t=C("ul");for(let s=0;s<a.length;s+=1)a[s].c();b(e,"class","sm:text-lg text-sm mb-2"),b(t,"class","font-light grid sm:grid-cols-2 grid-cols-1 sm:text-sm text-xs")},m(s,c){g(s,e,c),g(s,n,c),g(s,t,c);for(let f=0;f<a.length;f+=1)a[f]&&a[f].m(t,null);o=!0},p(s,c){if(c&0){r=k(U);let f;for(f=0;f<r.length;f+=1){const x=j(s,r,f);a[f]?(a[f].p(x,c),u(a[f],1)):(a[f]=P(x),a[f].c(),u(a[f],1),a[f].m(t,null))}for(q(),f=r.length;f<a.length;f+=1)l(f);I()}},i(s){if(!o){for(let c=0;c<r.length;c+=1)u(a[c]);o=!0}},o(s){a=a.filter(Boolean);for(let c=0;c<a.length;c+=1)i(a[c]);o=!1},d(s){s&&(d(e),d(n),d(t)),L(a,s)}}}function Q($){let e;return{c(){e=v("Upgrade to Pro")},m(n,t){g(n,e,t)},d(n){n&&d(e)}}}function R($){let e,n;return e=new y({props:{href:invoize.pricing_url,class:"w-full hover:text-white",target:"_blank",$$slots:{default:[Q]},$$scope:{ctx:$}}}),{c(){m(e.$$.fragment)},m(t,o){p(e,t,o),n=!0},p(t,o){const r={};o&8&&(r.$$scope={dirty:o,ctx:t}),e.$set(r)},i(t){n||(u(e.$$.fragment,t),n=!0)},o(t){i(e.$$.fragment,t),n=!1},d(t){_(e,t)}}}function V($){let e,n,t,o,r,a;return e=new K({props:{$$slots:{default:[E]},$$scope:{ctx:$}}}),t=new H({props:{class:"space-y-1 text-sm",$$slots:{default:[N]},$$scope:{ctx:$}}}),r=new J({props:{class:"flex justify-center",$$slots:{default:[R]},$$scope:{ctx:$}}}),{c(){m(e.$$.fragment),n=h(),m(t.$$.fragment),o=h(),m(r.$$.fragment)},m(l,s){p(e,l,s),g(l,n,s),p(t,l,s),g(l,o,s),p(r,l,s),a=!0},p(l,s){const c={};s&8&&(c.$$scope={dirty:s,ctx:l}),e.$set(c);const f={};s&8&&(f.$$scope={dirty:s,ctx:l}),t.$set(f);const x={};s&8&&(x.$$scope={dirty:s,ctx:l}),r.$set(x)},i(l){a||(u(e.$$.fragment,l),u(t.$$.fragment,l),u(r.$$.fragment,l),a=!0)},o(l){i(e.$$.fragment,l),i(t.$$.fragment,l),i(r.$$.fragment,l),a=!1},d(l){l&&(d(n),d(o)),_(e,l),_(t,l),_(r,l)}}}function W($){let e,n;return e=new G({props:{$$slots:{default:[V]},$$scope:{ctx:$}}}),{c(){m(e.$$.fragment)},m(t,o){p(e,t,o),n=!0},p(t,[o]){const r={};o&8&&(r.$$scope={dirty:o,ctx:t}),e.$set(r)},i(t){n||(u(e.$$.fragment,t),n=!0)},o(t){i(e.$$.fragment,t),n=!1},d(t){_(e,t)}}}class ne extends S{constructor(e){super(),z(this,e,null,W,B,{})}}export{ne as U};
