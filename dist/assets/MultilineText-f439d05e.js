import{S as w,b as S,s as N,k as u,d,e as _,g as b,h as a,j as k,F as o,u as E,G as m,m as v,H as T,bs as x}from"./settings-store-9c8686e2.js";function p(r,t,c){const n=r.slice();return n[2]=t[c].text,n[5]=t[c].br,n}function H(r){let t,c=k(r[3]),n=[];for(let e=0;e<c.length;e+=1)n[e]=y(p(r,c,e));let l=null;return c.length||(l=h(r)),{c(){for(let e=0;e<n.length;e+=1)n[e].c();t=o(),l&&l.c()},m(e,f){for(let i=0;i<n.length;i+=1)n[i]&&n[i].m(e,f);_(e,t,f),l&&l.m(e,f)},p(e,f){if(f&9){c=k(e[3]);let i;for(i=0;i<c.length;i+=1){const s=p(e,c,i);n[i]?n[i].p(s,f):(n[i]=y(s),n[i].c(),n[i].m(t.parentNode,t))}for(;i<n.length;i+=1)n[i].d(1);n.length=c.length,!c.length&&l?l.p(e,f):c.length?l&&(l.d(1),l=null):(l=h(e),l.c(),l.m(t.parentNode,t))}},d(e){e&&a(t),E(n,e),l&&l.d(e)}}}function M(r){let t;function c(e,f){return e[0]?C:B}let n=c(r),l=n(r);return{c(){l.c(),t=o()},m(e,f){l.m(e,f),_(e,t,f)},p(e,f){n!==(n=c(e))&&(l.d(1),l=n(e),l&&(l.c(),l.m(t.parentNode,t)))},d(e){e&&a(t),l.d(e)}}}function h(r){let t;function c(e,f){return e[0]?q:j}let n=c(r),l=n(r);return{c(){l.c(),t=o()},m(e,f){l.m(e,f),_(e,t,f)},p(e,f){n!==(n=c(e))&&(l.d(1),l=n(e),l&&(l.c(),l.m(t.parentNode,t)))},d(e){e&&a(t),l.d(e)}}}function j(r){let t;return{c(){t=m("-")},m(c,n){_(c,t,n)},d(c){c&&a(t)}}}function q(r){let t;return{c(){t=u("span")},m(c,n){_(c,t,n)},d(c){c&&a(t)}}}function y(r){let t=r[2]+"",c,n,l,e=r[5]+"",f;return{c(){c=m(t),n=v(),l=new x(!1),f=o(),l.a=f},m(i,s){_(i,c,s),_(i,n,s),l.m(e,i,s),_(i,f,s)},p(i,s){s&8&&t!==(t=i[2]+"")&&T(c,t),s&8&&e!==(e=i[5]+"")&&l.p(e)},d(i){i&&(a(c),a(n),a(f),l.d())}}}function B(r){let t;return{c(){t=m("-")},m(c,n){_(c,t,n)},d(c){c&&a(t)}}}function C(r){let t;return{c(){t=u("span")},m(c,n){_(c,t,n)},d(c){c&&a(t)}}}function F(r){let t;function c(e,f){var i,s;return((i=e[3])==null?void 0:i.length)<=1&&((s=e[3][0])==null?void 0:s.text)===""?M:H}let n=c(r),l=n(r);return{c(){t=u("div"),l.c(),d(t,"class",r[1])},m(e,f){_(e,t,f),l.m(t,null)},p(e,[f]){n===(n=c(e))&&l?l.p(e,f):(l.d(1),l=n(e),l&&(l.c(),l.m(t,null))),f&2&&d(t,"class",e[1])},i:b,o:b,d(e){e&&a(t),l.d()}}}function G(r,t,c){let n,{isShowEmpty:l=!1}=t,{text:e}=t,{class:f=""}=t;const i=s=>s?s.split(/\n|\r\n/g).map(g=>({text:g,br:"<br>"})):[];return r.$$set=s=>{"isShowEmpty"in s&&c(0,l=s.isShowEmpty),"text"in s&&c(2,e=s.text),"class"in s&&c(1,f=s.class)},r.$$.update=()=>{r.$$.dirty&4&&c(3,n=i(e??""))},[l,f,e,n]}class z extends w{constructor(t){super(),S(this,t,G,F,N,{isShowEmpty:0,text:2,class:1})}}export{z as M};
