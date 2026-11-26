<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Appy API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
                    body .content .php-example code { display: none; }
            </style>

    <style>
        /* Show sidebar submenus expanded by default */
        .tocify-wrapper .tocify-subheader {
            display: block !important;
        }
        /* Fix scroll offset when clicking sidebar links */
        html {
            scroll-padding-top: 0;
        }
        h1[id], h2[id] {
            scroll-margin-top: 10px;
        }
        /* Remove border above last updated */
        .toc-footer#last-updated {
            border-top: none;
        }
    </style>

    <script>
        var tryItOutBaseUrl = window.location.origin;
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.6.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.6.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;,&quot;php&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                                            <button type="button" class="lang-button" data-language-name="php">php</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div style="padding: 10px 15px 15px; border-bottom: 1px solid #404b55; overflow: hidden;">
        <label style="display: block; font-size: 0.85em; color: #939da3; margin-bottom: 6px;">API Token</label>
        <input type="text" id="global-auth-token" placeholder="Paste your token here" style="width: 100%; padding: 8px 10px; font-size: 12px; background: #1a1a1a; border: 1px solid #404b55; border-radius: 4px; color: #f4f4f4; outline: none; box-sizing: border-box;">
        <p style="font-size: 0.7em; color: #666; margin-top: 5px;">Auto-fills Authorization header</p>
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authentication" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authentication">
                    <a href="#authentication">Authentication</a>
                </li>
                            </ul>
                    <ul id="tocify-header-account" class="tocify-header">
                <li class="tocify-item level-1" data-unique="account">
                    <a href="#account">Account</a>
                </li>
                                    <ul id="tocify-subheader-account" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="account-GETapi-v1-account">
                                <a href="#account-GETapi-v1-account">Get account information</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="account-GETapi-v1-account-subscription">
                                <a href="#account-GETapi-v1-account-subscription">Get subscription information</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="account-GETapi-v1-account-usage">
                                <a href="#account-GETapi-v1-account-usage">Get usage statistics</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-apps" class="tocify-header">
                <li class="tocify-item level-1" data-unique="apps">
                    <a href="#apps">Apps</a>
                </li>
                                    <ul id="tocify-subheader-apps" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="apps-GETapi-v1-apps">
                                <a href="#apps-GETapi-v1-apps">List all apps</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="apps-POSTapi-v1-apps">
                                <a href="#apps-POSTapi-v1-apps">Create a new app</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="apps-GETapi-v1-apps--id-">
                                <a href="#apps-GETapi-v1-apps--id-">Get app details</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="apps-PUTapi-v1-apps--id-">
                                <a href="#apps-PUTapi-v1-apps--id-">Update an app</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="apps-DELETEapi-v1-apps--id-">
                                <a href="#apps-DELETEapi-v1-apps--id-">Delete an app</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-builds" class="tocify-header">
                <li class="tocify-item level-1" data-unique="builds">
                    <a href="#builds">Builds</a>
                </li>
                                    <ul id="tocify-subheader-builds" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="builds-GETapi-v1-apps--app_id--builds">
                                <a href="#builds-GETapi-v1-apps--app_id--builds">List builds for an app</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="builds-POSTapi-v1-apps--app_id--builds">
                                <a href="#builds-POSTapi-v1-apps--app_id--builds">Trigger a new build</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="builds-GETapi-v1-builds--build_id-">
                                <a href="#builds-GETapi-v1-builds--build_id-">Get build details</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="builds-GETapi-v1-builds--build_id--download">
                                <a href="#builds-GETapi-v1-builds--build_id--download">Download build artifact</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="builds-GETapi-v1-builds--build_id--logs">
                                <a href="#builds-GETapi-v1-builds--build_id--logs">Get build logs</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-notifications" class="tocify-header">
                <li class="tocify-item level-1" data-unique="notifications">
                    <a href="#notifications">Notifications</a>
                </li>
                                    <ul id="tocify-subheader-notifications" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="notifications-GETapi-v1-apps--app_id--notifications">
                                <a href="#notifications-GETapi-v1-apps--app_id--notifications">List notifications for an app</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="notifications-POSTapi-v1-apps--app_id--notifications">
                                <a href="#notifications-POSTapi-v1-apps--app_id--notifications">Send a push notification</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="notifications-GETapi-v1-notifications--notification_id-">
                                <a href="#notifications-GETapi-v1-notifications--notification_id-">Get notification details</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="notifications-DELETEapi-v1-notifications--notification_id-">
                                <a href="#notifications-DELETEapi-v1-notifications--notification_id-">Cancel a scheduled notification</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: December 23, 2025</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<p>Programmatic access to your apps, builds, and push notifications.</p>
<p>This API allows you to programmatically manage your apps, trigger builds, and send push notifications.</p>

        <h1 id="authentication">Authentication</h1>
<p>To authenticate requests, include an <strong><code>Authorization</code></strong> header with the value <strong><code>"Bearer {YOUR_API_TOKEN}"</code></strong>.</p>
<p>All authenticated endpoints are marked with a <code>requires authentication</code> badge in the documentation below.</p>
<p>Get your API token from the <a href="/user/api-keys">API Keys</a> page in your dashboard.</p>

        <h1 id="account">Account</h1>

    <p>APIs for retrieving account information, subscription details, and usage statistics.</p>

                                <h2 id="account-GETapi-v1-account">Get account information</h2>

<p>
<small class="badge badge-darkred">Requires Authentication</small>
</p>

<p>Retrieve the authenticated user's account details including name, email, and plan information.</p>

<span id="example-requests-GETapi-v1-account">
<blockquote>Example request:</blockquote>
<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "/api/v1/account" \
    --header "Authorization: Bearer {YOUR_API_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>
<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "/api/v1/account"
);

const headers = {
    "Authorization": "Bearer {YOUR_API_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>
<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '/api/v1/account';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_API_TOKEN}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>
</span>

<span id="example-responses-GETapi-v1-account">
            <blockquote>
            <p>Example response (200, Success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;John Doe&quot;,
        &quot;email&quot;: &quot;john@example.com&quot;,
        &quot;plan&quot;: {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;Pro&quot;
        },
        &quot;build_credits&quot;: 50,
        &quot;created_at&quot;: &quot;2024-01-01T00:00:00.000000Z&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-account" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-account"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-account"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-account" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-account">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-account" data-method="GET"
      data-path="api/v1/account"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-account', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-account"
                    onclick="tryItOut('GETapi-v1-account');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-account"
                    onclick="cancelTryOut('GETapi-v1-account');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-account"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/account</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-account"
               value="Bearer {YOUR_API_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_API_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-account"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-account"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="account-GETapi-v1-account-subscription">Get subscription information</h2>

<p>
<small class="badge badge-darkred">Requires Authentication</small>
</p>

<p>Retrieve the authenticated user's current subscription details including plan, status, and renewal date.</p>

<span id="example-requests-GETapi-v1-account-subscription">
<blockquote>Example request:</blockquote>
<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "/api/v1/account/subscription" \
    --header "Authorization: Bearer {YOUR_API_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>
<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "/api/v1/account/subscription"
);

const headers = {
    "Authorization": "Bearer {YOUR_API_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>
<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '/api/v1/account/subscription';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_API_TOKEN}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>
</span>

<span id="example-responses-GETapi-v1-account-subscription">
            <blockquote>
            <p>Example response (200, Active subscription):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;status&quot;: &quot;active&quot;,
        &quot;amount&quot;: 29.99,
        &quot;payment_method&quot;: &quot;stripe&quot;,
        &quot;plan&quot;: {
            &quot;id&quot;: 2,
            &quot;name&quot;: &quot;Pro&quot;,
            &quot;price&quot;: 29.99
        },
        &quot;renewal_at&quot;: &quot;2024-02-01T00:00:00.000000Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (200, No subscription):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;No active subscription found.&quot;,
    &quot;plan&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;Free&quot;,
        &quot;price&quot;: 0
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-account-subscription" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-account-subscription"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-account-subscription"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-account-subscription" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-account-subscription">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-account-subscription" data-method="GET"
      data-path="api/v1/account/subscription"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-account-subscription', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-account-subscription"
                    onclick="tryItOut('GETapi-v1-account-subscription');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-account-subscription"
                    onclick="cancelTryOut('GETapi-v1-account-subscription');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-account-subscription"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/account/subscription</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-account-subscription"
               value="Bearer {YOUR_API_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_API_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-account-subscription"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-account-subscription"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="account-GETapi-v1-account-usage">Get usage statistics</h2>

<p>
<small class="badge badge-darkred">Requires Authentication</small>
</p>

<p>Retrieve the authenticated user's usage statistics including build credits, app counts, and build history.</p>

<span id="example-requests-GETapi-v1-account-usage">
<blockquote>Example request:</blockquote>
<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "/api/v1/account/usage" \
    --header "Authorization: Bearer {YOUR_API_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>
<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "/api/v1/account/usage"
);

const headers = {
    "Authorization": "Bearer {YOUR_API_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>
<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '/api/v1/account/usage';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_API_TOKEN}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>
</span>

<span id="example-responses-GETapi-v1-account-usage">
            <blockquote>
            <p>Example response (200, Success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;build_credits&quot;: {
        &quot;available&quot;: 45,
        &quot;monthly_allocation&quot;: 100,
        &quot;unlimited&quot;: false
    },
    &quot;apps&quot;: {
        &quot;total&quot;: 5
    },
    &quot;builds&quot;: {
        &quot;total&quot;: 23,
        &quot;completed&quot;: 20,
        &quot;failed&quot;: 3
    },
    &quot;plan&quot;: {
        &quot;id&quot;: 2,
        &quot;name&quot;: &quot;Pro&quot;,
        &quot;features&quot;: [
            &quot;Unlimited apps&quot;,
            &quot;Priority support&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-account-usage" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-account-usage"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-account-usage"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-account-usage" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-account-usage">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-account-usage" data-method="GET"
      data-path="api/v1/account/usage"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-account-usage', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-account-usage"
                    onclick="tryItOut('GETapi-v1-account-usage');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-account-usage"
                    onclick="cancelTryOut('GETapi-v1-account-usage');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-account-usage"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/account/usage</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-account-usage"
               value="Bearer {YOUR_API_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_API_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-account-usage"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-account-usage"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="apps">Apps</h1>

    <p>APIs for managing your mobile applications.</p>

                                <h2 id="apps-GETapi-v1-apps">List all apps</h2>

<p>
<small class="badge badge-darkred">Requires Authentication</small>
</p>

<p>Get a paginated list of all apps belonging to the authenticated user.</p>

<span id="example-requests-GETapi-v1-apps">
<blockquote>Example request:</blockquote>
<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "/api/v1/apps?per_page=10" \
    --header "Authorization: Bearer {YOUR_API_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>
<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "/api/v1/apps"
);

const params = {
    "per_page": "10",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {YOUR_API_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>
<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '/api/v1/apps';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_API_TOKEN}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'per_page' =&gt; '10',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>
</span>

<span id="example-responses-GETapi-v1-apps">
            <blockquote>
            <p>Example response (200, Success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;My App&quot;,
            &quot;platform&quot;: &quot;android-webview&quot;,
            &quot;version_name&quot;: &quot;1.0.0&quot;,
            &quot;version_code&quot;: 1
        }
    ],
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;total&quot;: 5
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-apps" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-apps"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-apps"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-apps" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-apps">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-apps" data-method="GET"
      data-path="api/v1/apps"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-apps', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-apps"
                    onclick="tryItOut('GETapi-v1-apps');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-apps"
                    onclick="cancelTryOut('GETapi-v1-apps');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-apps"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/apps</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-apps"
               value="Bearer {YOUR_API_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_API_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-apps"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-apps"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-v1-apps"
               value="10"
               data-component="query">
    <br>
<p>Number of items per page. Default: 15. Example: <code>10</code></p>
            </div>
                </form>

                    <h2 id="apps-POSTapi-v1-apps">Create a new app</h2>

<p>
<small class="badge badge-darkred">Requires Authentication</small>
</p>

<p>Create a new app for the authenticated user.</p>

<span id="example-requests-POSTapi-v1-apps">
<blockquote>Example request:</blockquote>
<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "/api/v1/apps" \
    --header "Authorization: Bearer {YOUR_API_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"My App\",
    \"platform\": \"android-webview\",
    \"url\": \"https:\\/\\/example.com\",
    \"package_name\": \"com.example.myapp\",
    \"version_name\": \"1.0.0\",
    \"version_code\": 1
}"
</code></pre></div>
<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "/api/v1/apps"
);

const headers = {
    "Authorization": "Bearer {YOUR_API_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "My App",
    "platform": "android-webview",
    "url": "https:\/\/example.com",
    "package_name": "com.example.myapp",
    "version_name": "1.0.0",
    "version_code": 1
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>
<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '/api/v1/apps';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_API_TOKEN}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'name' =&gt; 'My App',
            'platform' =&gt; 'android-webview',
            'url' =&gt; 'https://example.com',
            'package_name' =&gt; 'com.example.myapp',
            'version_name' =&gt; '1.0.0',
            'version_code' =&gt; 1,
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>
</span>

<span id="example-responses-POSTapi-v1-apps">
            <blockquote>
            <p>Example response (201, Success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;My App&quot;,
        &quot;platform&quot;: &quot;android-webview&quot;,
        &quot;version_name&quot;: &quot;1.0.0&quot;,
        &quot;version_code&quot;: 1,
        &quot;url&quot;: &quot;https://example.com&quot;,
        &quot;package_name&quot;: &quot;com.example.myapp&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Validation Error):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;The name field is required.&quot;,
    &quot;errors&quot;: {
        &quot;name&quot;: [
            &quot;The name field is required.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-apps" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-apps"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-apps"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-apps" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-apps">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-apps" data-method="POST"
      data-path="api/v1/apps"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-apps', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-apps"
                    onclick="tryItOut('POSTapi-v1-apps');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-apps"
                    onclick="cancelTryOut('POSTapi-v1-apps');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-apps"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/apps</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-v1-apps"
               value="Bearer {YOUR_API_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_API_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-apps"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-apps"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-v1-apps"
               value="My App"
               data-component="body">
    <br>
<p>The name of the app. Example: <code>My App</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>platform</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="platform"                data-endpoint="POSTapi-v1-apps"
               value="android-webview"
               data-component="body">
    <br>
<p>The platform slug (e.g., android-webview). Example: <code>android-webview</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>url</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="url"                data-endpoint="POSTapi-v1-apps"
               value="https://example.com"
               data-component="body">
    <br>
<p>The website URL for the app. Example: <code>https://example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>package_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="package_name"                data-endpoint="POSTapi-v1-apps"
               value="com.example.myapp"
               data-component="body">
    <br>
<p>The package name (e.g., com.example.app). Example: <code>com.example.myapp</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>version_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="version_name"                data-endpoint="POSTapi-v1-apps"
               value="1.0.0"
               data-component="body">
    <br>
<p>The initial version name. Default: 1.0.0. Example: <code>1.0.0</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>version_code</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="version_code"                data-endpoint="POSTapi-v1-apps"
               value="1"
               data-component="body">
    <br>
<p>The initial version code. Default: 1. Example: <code>1</code></p>
        </div>
        </form>

                    <h2 id="apps-GETapi-v1-apps--id-">Get app details</h2>

<p>
<small class="badge badge-darkred">Requires Authentication</small>
</p>

<p>Get details for a specific app including recent builds.</p>

<span id="example-requests-GETapi-v1-apps--id-">
<blockquote>Example request:</blockquote>
<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "/api/v1/apps/2" \
    --header "Authorization: Bearer {YOUR_API_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>
<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "/api/v1/apps/2"
);

const headers = {
    "Authorization": "Bearer {YOUR_API_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>
<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '/api/v1/apps/2';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_API_TOKEN}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>
</span>

<span id="example-responses-GETapi-v1-apps--id-">
            <blockquote>
            <p>Example response (200, Success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;My App&quot;,
        &quot;platform&quot;: &quot;android-webview&quot;,
        &quot;version_name&quot;: &quot;1.0.0&quot;,
        &quot;version_code&quot;: 1,
        &quot;builds&quot;: []
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Not Found):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;App not found.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-apps--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-apps--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-apps--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-apps--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-apps--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-apps--id-" data-method="GET"
      data-path="api/v1/apps/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-apps--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-apps--id-"
                    onclick="tryItOut('GETapi-v1-apps--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-apps--id-"
                    onclick="cancelTryOut('GETapi-v1-apps--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-apps--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/apps/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-apps--id-"
               value="Bearer {YOUR_API_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_API_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-apps--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-apps--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-v1-apps--id-"
               value="2"
               data-component="url">
    <br>
<p>The ID of the app. Example: <code>2</code></p>
            </div>
                    </form>

                    <h2 id="apps-PUTapi-v1-apps--id-">Update an app</h2>

<p>
<small class="badge badge-darkred">Requires Authentication</small>
</p>

<p>Update an existing app's details and configuration.</p>

<span id="example-requests-PUTapi-v1-apps--id-">
<blockquote>Example request:</blockquote>
<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "/api/v1/apps/2" \
    --header "Authorization: Bearer {YOUR_API_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Updated App Name\",
    \"url\": \"https:\\/\\/newsite.com\",
    \"package_name\": \"com.example.newapp\",
    \"version_name\": \"1.0.1\",
    \"version_code\": 2
}"
</code></pre></div>
<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "/api/v1/apps/2"
);

const headers = {
    "Authorization": "Bearer {YOUR_API_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "Updated App Name",
    "url": "https:\/\/newsite.com",
    "package_name": "com.example.newapp",
    "version_name": "1.0.1",
    "version_code": 2
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>
<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '/api/v1/apps/2';
$response = $client-&gt;put(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_API_TOKEN}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'name' =&gt; 'Updated App Name',
            'url' =&gt; 'https://newsite.com',
            'package_name' =&gt; 'com.example.newapp',
            'version_name' =&gt; '1.0.1',
            'version_code' =&gt; 2,
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>
</span>

<span id="example-responses-PUTapi-v1-apps--id-">
            <blockquote>
            <p>Example response (200, Success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;Updated App Name&quot;,
        &quot;platform&quot;: &quot;android-webview&quot;,
        &quot;version_name&quot;: &quot;1.0.1&quot;,
        &quot;version_code&quot;: 2
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Not Found):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;App not found.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-v1-apps--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-v1-apps--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-v1-apps--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-v1-apps--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-v1-apps--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-v1-apps--id-" data-method="PUT"
      data-path="api/v1/apps/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-v1-apps--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-v1-apps--id-"
                    onclick="tryItOut('PUTapi-v1-apps--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-v1-apps--id-"
                    onclick="cancelTryOut('PUTapi-v1-apps--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-v1-apps--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/v1/apps/{id}</code></b>
        </p>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/v1/apps/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-v1-apps--id-"
               value="Bearer {YOUR_API_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_API_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-v1-apps--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-v1-apps--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PUTapi-v1-apps--id-"
               value="2"
               data-component="url">
    <br>
<p>The ID of the app. Example: <code>2</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-v1-apps--id-"
               value="Updated App Name"
               data-component="body">
    <br>
<p>The app name. Example: <code>Updated App Name</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>url</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="url"                data-endpoint="PUTapi-v1-apps--id-"
               value="https://newsite.com"
               data-component="body">
    <br>
<p>The website URL. Example: <code>https://newsite.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>package_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="package_name"                data-endpoint="PUTapi-v1-apps--id-"
               value="com.example.newapp"
               data-component="body">
    <br>
<p>The package name. Example: <code>com.example.newapp</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>version_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="version_name"                data-endpoint="PUTapi-v1-apps--id-"
               value="1.0.1"
               data-component="body">
    <br>
<p>The version name. Example: <code>1.0.1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>version_code</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="version_code"                data-endpoint="PUTapi-v1-apps--id-"
               value="2"
               data-component="body">
    <br>
<p>The version code. Example: <code>2</code></p>
        </div>
        </form>

                    <h2 id="apps-DELETEapi-v1-apps--id-">Delete an app</h2>

<p>
<small class="badge badge-darkred">Requires Authentication</small>
</p>

<p>Delete an app and all its associated data including builds and configurations.</p>

<span id="example-requests-DELETEapi-v1-apps--id-">
<blockquote>Example request:</blockquote>
<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "/api/v1/apps/2" \
    --header "Authorization: Bearer {YOUR_API_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>
<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "/api/v1/apps/2"
);

const headers = {
    "Authorization": "Bearer {YOUR_API_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>
<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '/api/v1/apps/2';
$response = $client-&gt;delete(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_API_TOKEN}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>
</span>

<span id="example-responses-DELETEapi-v1-apps--id-">
            <blockquote>
            <p>Example response (200, Success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;App deleted successfully.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Not Found):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;App not found.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (409, Has Active Builds):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Cannot delete app with active builds.&quot;,
    &quot;error&quot;: &quot;active_builds&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-v1-apps--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-apps--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-apps--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-apps--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-apps--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-apps--id-" data-method="DELETE"
      data-path="api/v1/apps/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-apps--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-apps--id-"
                    onclick="tryItOut('DELETEapi-v1-apps--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-apps--id-"
                    onclick="cancelTryOut('DELETEapi-v1-apps--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-apps--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/apps/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-v1-apps--id-"
               value="Bearer {YOUR_API_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_API_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-apps--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-v1-apps--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-v1-apps--id-"
               value="2"
               data-component="url">
    <br>
<p>The ID of the app. Example: <code>2</code></p>
            </div>
                    </form>

                <h1 id="builds">Builds</h1>

    <p>APIs for managing app builds. Trigger new builds, check status, and download artifacts.</p>

                                <h2 id="builds-GETapi-v1-apps--app_id--builds">List builds for an app</h2>

<p>
<small class="badge badge-darkred">Requires Authentication</small>
</p>

<p>Get a paginated list of builds for a specific app.</p>

<span id="example-requests-GETapi-v1-apps--app_id--builds">
<blockquote>Example request:</blockquote>
<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "/api/v1/apps/2/builds?per_page=10" \
    --header "Authorization: Bearer {YOUR_API_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>
<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "/api/v1/apps/2/builds"
);

const params = {
    "per_page": "10",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {YOUR_API_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>
<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '/api/v1/apps/2/builds';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_API_TOKEN}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'per_page' =&gt; '10',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>
</span>

<span id="example-responses-GETapi-v1-apps--app_id--builds">
            <blockquote>
            <p>Example response (200, Success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;status&quot;: &quot;completed&quot;,
            &quot;build_type&quot;: &quot;debug&quot;,
            &quot;build_format&quot;: &quot;apk&quot;,
            &quot;version_name&quot;: &quot;1.0.0&quot;,
            &quot;version_code&quot;: 1,
            &quot;created_at&quot;: &quot;2024-01-01T00:00:00.000000Z&quot;
        }
    ],
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;total&quot;: 5
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-apps--app_id--builds" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-apps--app_id--builds"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-apps--app_id--builds"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-apps--app_id--builds" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-apps--app_id--builds">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-apps--app_id--builds" data-method="GET"
      data-path="api/v1/apps/{app_id}/builds"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-apps--app_id--builds', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-apps--app_id--builds"
                    onclick="tryItOut('GETapi-v1-apps--app_id--builds');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-apps--app_id--builds"
                    onclick="cancelTryOut('GETapi-v1-apps--app_id--builds');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-apps--app_id--builds"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/apps/{app_id}/builds</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-apps--app_id--builds"
               value="Bearer {YOUR_API_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_API_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-apps--app_id--builds"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-apps--app_id--builds"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>app_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="app_id"                data-endpoint="GETapi-v1-apps--app_id--builds"
               value="2"
               data-component="url">
    <br>
<p>The ID of the app. Example: <code>2</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-v1-apps--app_id--builds"
               value="10"
               data-component="query">
    <br>
<p>Number of items per page. Default: 15. Example: <code>10</code></p>
            </div>
                </form>

                    <h2 id="builds-POSTapi-v1-apps--app_id--builds">Trigger a new build</h2>

<p>
<small class="badge badge-darkred">Requires Authentication</small>
</p>

<p>Trigger a new build for a specific app. Requires available build credits.</p>

<span id="example-requests-POSTapi-v1-apps--app_id--builds">
<blockquote>Example request:</blockquote>
<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "/api/v1/apps/2/builds" \
    --header "Authorization: Bearer {YOUR_API_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"build_format\": \"apk\",
    \"build_type\": \"debug\",
    \"keystore_id\": 1
}"
</code></pre></div>
<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "/api/v1/apps/2/builds"
);

const headers = {
    "Authorization": "Bearer {YOUR_API_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "build_format": "apk",
    "build_type": "debug",
    "keystore_id": 1
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>
<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '/api/v1/apps/2/builds';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_API_TOKEN}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'build_format' =&gt; 'apk',
            'build_type' =&gt; 'debug',
            'keystore_id' =&gt; 1,
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>
</span>

<span id="example-responses-POSTapi-v1-apps--app_id--builds">
            <blockquote>
            <p>Example response (201, Success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;status&quot;: &quot;pending&quot;,
        &quot;build_type&quot;: &quot;debug&quot;,
        &quot;build_format&quot;: &quot;apk&quot;,
        &quot;version_name&quot;: &quot;1.0.1&quot;,
        &quot;version_code&quot;: 2
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (402, Insufficient Credits):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Insufficient build credits.&quot;,
    &quot;error&quot;: &quot;insufficient_credits&quot;,
    &quot;required_credits&quot;: 1,
    &quot;available_credits&quot;: 0
}</code>
 </pre>
            <blockquote>
            <p>Example response (409, Active Build):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;App already has an active build in progress.&quot;,
    &quot;error&quot;: &quot;active_build&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (503, No Builders Available):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;No builders available. Please try again later.&quot;,
    &quot;error&quot;: &quot;no_builders&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-apps--app_id--builds" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-apps--app_id--builds"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-apps--app_id--builds"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-apps--app_id--builds" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-apps--app_id--builds">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-apps--app_id--builds" data-method="POST"
      data-path="api/v1/apps/{app_id}/builds"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-apps--app_id--builds', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-apps--app_id--builds"
                    onclick="tryItOut('POSTapi-v1-apps--app_id--builds');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-apps--app_id--builds"
                    onclick="cancelTryOut('POSTapi-v1-apps--app_id--builds');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-apps--app_id--builds"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/apps/{app_id}/builds</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-v1-apps--app_id--builds"
               value="Bearer {YOUR_API_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_API_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-apps--app_id--builds"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-apps--app_id--builds"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>app_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="app_id"                data-endpoint="POSTapi-v1-apps--app_id--builds"
               value="2"
               data-component="url">
    <br>
<p>The ID of the app. Example: <code>2</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>build_format</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="build_format"                data-endpoint="POSTapi-v1-apps--app_id--builds"
               value="apk"
               data-component="body">
    <br>
<p>The build format: apk or aab. Default: apk. Example: <code>apk</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>build_type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="build_type"                data-endpoint="POSTapi-v1-apps--app_id--builds"
               value="debug"
               data-component="body">
    <br>
<p>The build type: debug or release. Default: debug. Example: <code>debug</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>keystore_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="keystore_id"                data-endpoint="POSTapi-v1-apps--app_id--builds"
               value="1"
               data-component="body">
    <br>
<p>The keystore ID for release builds. Example: <code>1</code></p>
        </div>
        </form>

                    <h2 id="builds-GETapi-v1-builds--build_id-">Get build details</h2>

<p>
<small class="badge badge-darkred">Requires Authentication</small>
</p>

<p>Get details for a specific build including status and logs.</p>

<span id="example-requests-GETapi-v1-builds--build_id-">
<blockquote>Example request:</blockquote>
<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "/api/v1/builds/1" \
    --header "Authorization: Bearer {YOUR_API_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>
<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "/api/v1/builds/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_API_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>
<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '/api/v1/builds/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_API_TOKEN}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>
</span>

<span id="example-responses-GETapi-v1-builds--build_id-">
            <blockquote>
            <p>Example response (200, Success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;status&quot;: &quot;completed&quot;,
        &quot;build_type&quot;: &quot;debug&quot;,
        &quot;build_format&quot;: &quot;apk&quot;,
        &quot;version_name&quot;: &quot;1.0.0&quot;,
        &quot;version_code&quot;: 1,
        &quot;artifact_size&quot;: 15000000,
        &quot;build_duration&quot;: 120,
        &quot;created_at&quot;: &quot;2024-01-01T00:00:00.000000Z&quot;,
        &quot;completed_at&quot;: &quot;2024-01-01T00:02:00.000000Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Not Found):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Build not found.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-builds--build_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-builds--build_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-builds--build_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-builds--build_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-builds--build_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-builds--build_id-" data-method="GET"
      data-path="api/v1/builds/{build_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-builds--build_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-builds--build_id-"
                    onclick="tryItOut('GETapi-v1-builds--build_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-builds--build_id-"
                    onclick="cancelTryOut('GETapi-v1-builds--build_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-builds--build_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/builds/{build_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-builds--build_id-"
               value="Bearer {YOUR_API_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_API_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-builds--build_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-builds--build_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>build_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="build_id"                data-endpoint="GETapi-v1-builds--build_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the build. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="builds-GETapi-v1-builds--build_id--download">Download build artifact</h2>

<p>
<small class="badge badge-darkred">Requires Authentication</small>
</p>

<p>Get the download URL for a completed build artifact.</p>

<span id="example-requests-GETapi-v1-builds--build_id--download">
<blockquote>Example request:</blockquote>
<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "/api/v1/builds/1/download" \
    --header "Authorization: Bearer {YOUR_API_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>
<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "/api/v1/builds/1/download"
);

const headers = {
    "Authorization": "Bearer {YOUR_API_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>
<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '/api/v1/builds/1/download';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_API_TOKEN}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>
</span>

<span id="example-responses-GETapi-v1-builds--build_id--download">
            <blockquote>
            <p>Example response (200, Success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;download_url&quot;: &quot;https://example.com/builds/app.apk?token=abc123&quot;,
    &quot;file_name&quot;: &quot;My App.apk&quot;,
    &quot;file_size&quot;: 15000000,
    &quot;expires_at&quot;: &quot;2024-01-08T00:00:00+00:00&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Build Not Completed):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Build is not completed yet.&quot;,
    &quot;error&quot;: &quot;build_not_completed&quot;,
    &quot;status&quot;: &quot;building&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Artifact Not Found):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Build artifact not available.&quot;,
    &quot;error&quot;: &quot;artifact_not_found&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-builds--build_id--download" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-builds--build_id--download"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-builds--build_id--download"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-builds--build_id--download" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-builds--build_id--download">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-builds--build_id--download" data-method="GET"
      data-path="api/v1/builds/{build_id}/download"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-builds--build_id--download', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-builds--build_id--download"
                    onclick="tryItOut('GETapi-v1-builds--build_id--download');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-builds--build_id--download"
                    onclick="cancelTryOut('GETapi-v1-builds--build_id--download');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-builds--build_id--download"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/builds/{build_id}/download</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-builds--build_id--download"
               value="Bearer {YOUR_API_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_API_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-builds--build_id--download"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-builds--build_id--download"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>build_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="build_id"                data-endpoint="GETapi-v1-builds--build_id--download"
               value="1"
               data-component="url">
    <br>
<p>The ID of the build. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="builds-GETapi-v1-builds--build_id--logs">Get build logs</h2>

<p>
<small class="badge badge-darkred">Requires Authentication</small>
</p>

<p>Get the build logs for a specific build. Useful for debugging failed builds.</p>

<span id="example-requests-GETapi-v1-builds--build_id--logs">
<blockquote>Example request:</blockquote>
<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "/api/v1/builds/1/logs" \
    --header "Authorization: Bearer {YOUR_API_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>
<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "/api/v1/builds/1/logs"
);

const headers = {
    "Authorization": "Bearer {YOUR_API_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>
<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '/api/v1/builds/1/logs';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_API_TOKEN}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>
</span>

<span id="example-responses-GETapi-v1-builds--build_id--logs">
            <blockquote>
            <p>Example response (200, Success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;build_id&quot;: 1,
    &quot;status&quot;: &quot;completed&quot;,
    &quot;logs&quot;: &quot;Build started...\nCompiling...\nBuild completed successfully.&quot;,
    &quot;error_message&quot;: null
}</code>
 </pre>
            <blockquote>
            <p>Example response (200, Failed Build):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;build_id&quot;: 2,
    &quot;status&quot;: &quot;failed&quot;,
    &quot;logs&quot;: &quot;Build started...\nCompiling...&quot;,
    &quot;error_message&quot;: &quot;Compilation failed: missing dependency&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-builds--build_id--logs" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-builds--build_id--logs"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-builds--build_id--logs"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-builds--build_id--logs" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-builds--build_id--logs">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-builds--build_id--logs" data-method="GET"
      data-path="api/v1/builds/{build_id}/logs"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-builds--build_id--logs', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-builds--build_id--logs"
                    onclick="tryItOut('GETapi-v1-builds--build_id--logs');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-builds--build_id--logs"
                    onclick="cancelTryOut('GETapi-v1-builds--build_id--logs');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-builds--build_id--logs"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/builds/{build_id}/logs</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-builds--build_id--logs"
               value="Bearer {YOUR_API_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_API_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-builds--build_id--logs"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-builds--build_id--logs"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>build_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="build_id"                data-endpoint="GETapi-v1-builds--build_id--logs"
               value="1"
               data-component="url">
    <br>
<p>The ID of the build. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="notifications">Notifications</h1>

    <p>APIs for sending and managing push notifications to your app users.</p>

                                <h2 id="notifications-GETapi-v1-apps--app_id--notifications">List notifications for an app</h2>

<p>
<small class="badge badge-darkred">Requires Authentication</small>
</p>

<p>Get a paginated list of push notifications sent to a specific app.</p>

<span id="example-requests-GETapi-v1-apps--app_id--notifications">
<blockquote>Example request:</blockquote>
<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "/api/v1/apps/2/notifications?per_page=10" \
    --header "Authorization: Bearer {YOUR_API_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>
<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "/api/v1/apps/2/notifications"
);

const params = {
    "per_page": "10",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {YOUR_API_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>
<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '/api/v1/apps/2/notifications';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_API_TOKEN}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'per_page' =&gt; '10',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>
</span>

<span id="example-responses-GETapi-v1-apps--app_id--notifications">
            <blockquote>
            <p>Example response (200, Success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;title&quot;: &quot;Welcome!&quot;,
            &quot;body&quot;: &quot;Thanks for installing our app.&quot;,
            &quot;status&quot;: &quot;sent&quot;,
            &quot;sent_at&quot;: &quot;2024-01-01T00:00:00.000000Z&quot;
        }
    ],
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;total&quot;: 10
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-apps--app_id--notifications" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-apps--app_id--notifications"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-apps--app_id--notifications"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-apps--app_id--notifications" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-apps--app_id--notifications">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-apps--app_id--notifications" data-method="GET"
      data-path="api/v1/apps/{app_id}/notifications"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-apps--app_id--notifications', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-apps--app_id--notifications"
                    onclick="tryItOut('GETapi-v1-apps--app_id--notifications');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-apps--app_id--notifications"
                    onclick="cancelTryOut('GETapi-v1-apps--app_id--notifications');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-apps--app_id--notifications"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/apps/{app_id}/notifications</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-apps--app_id--notifications"
               value="Bearer {YOUR_API_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_API_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-apps--app_id--notifications"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-apps--app_id--notifications"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>app_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="app_id"                data-endpoint="GETapi-v1-apps--app_id--notifications"
               value="2"
               data-component="url">
    <br>
<p>The ID of the app. Example: <code>2</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-v1-apps--app_id--notifications"
               value="10"
               data-component="query">
    <br>
<p>Number of items per page. Default: 15. Example: <code>10</code></p>
            </div>
                </form>

                    <h2 id="notifications-POSTapi-v1-apps--app_id--notifications">Send a push notification</h2>

<p>
<small class="badge badge-darkred">Requires Authentication</small>
</p>

<p>Send a push notification to all users of a specific app. Requires push notifications to be configured.</p>

<span id="example-requests-POSTapi-v1-apps--app_id--notifications">
<blockquote>Example request:</blockquote>
<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "/api/v1/apps/2/notifications" \
    --header "Authorization: Bearer {YOUR_API_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"title\": \"New Update Available\",
    \"body\": \"Check out our latest features!\",
    \"image_url\": \"https:\\/\\/example.com\\/image.png\",
    \"scheduled_at\": \"2024-01-15T10:00:00Z\"
}"
</code></pre></div>
<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "/api/v1/apps/2/notifications"
);

const headers = {
    "Authorization": "Bearer {YOUR_API_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "title": "New Update Available",
    "body": "Check out our latest features!",
    "image_url": "https:\/\/example.com\/image.png",
    "scheduled_at": "2024-01-15T10:00:00Z"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>
<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '/api/v1/apps/2/notifications';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_API_TOKEN}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'title' =&gt; 'New Update Available',
            'body' =&gt; 'Check out our latest features!',
            'image_url' =&gt; 'https://example.com/image.png',
            'scheduled_at' =&gt; '2024-01-15T10:00:00Z',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>
</span>

<span id="example-responses-POSTapi-v1-apps--app_id--notifications">
            <blockquote>
            <p>Example response (201, Success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;title&quot;: &quot;New Update Available&quot;,
        &quot;body&quot;: &quot;Check out our latest features!&quot;,
        &quot;status&quot;: &quot;pending&quot;,
        &quot;created_at&quot;: &quot;2024-01-01T00:00:00.000000Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Notifications Not Configured):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Push notifications are not configured for this app.&quot;,
    &quot;error&quot;: &quot;notifications_not_configured&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-apps--app_id--notifications" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-apps--app_id--notifications"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-apps--app_id--notifications"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-apps--app_id--notifications" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-apps--app_id--notifications">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-apps--app_id--notifications" data-method="POST"
      data-path="api/v1/apps/{app_id}/notifications"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-apps--app_id--notifications', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-apps--app_id--notifications"
                    onclick="tryItOut('POSTapi-v1-apps--app_id--notifications');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-apps--app_id--notifications"
                    onclick="cancelTryOut('POSTapi-v1-apps--app_id--notifications');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-apps--app_id--notifications"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/apps/{app_id}/notifications</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-v1-apps--app_id--notifications"
               value="Bearer {YOUR_API_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_API_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-apps--app_id--notifications"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-apps--app_id--notifications"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>app_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="app_id"                data-endpoint="POSTapi-v1-apps--app_id--notifications"
               value="2"
               data-component="url">
    <br>
<p>The ID of the app. Example: <code>2</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>title</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="title"                data-endpoint="POSTapi-v1-apps--app_id--notifications"
               value="New Update Available"
               data-component="body">
    <br>
<p>The notification title. Example: <code>New Update Available</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>body</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="body"                data-endpoint="POSTapi-v1-apps--app_id--notifications"
               value="Check out our latest features!"
               data-component="body">
    <br>
<p>The notification body/message. Example: <code>Check out our latest features!</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>image_url</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="image_url"                data-endpoint="POSTapi-v1-apps--app_id--notifications"
               value="https://example.com/image.png"
               data-component="body">
    <br>
<p>URL to an image to include in the notification. Example: <code>https://example.com/image.png</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>scheduled_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="scheduled_at"                data-endpoint="POSTapi-v1-apps--app_id--notifications"
               value="2024-01-15T10:00:00Z"
               data-component="body">
    <br>
<p>Schedule the notification for a future time (ISO 8601 format). Example: <code>2024-01-15T10:00:00Z</code></p>
        </div>
        </form>

                    <h2 id="notifications-GETapi-v1-notifications--notification_id-">Get notification details</h2>

<p>
<small class="badge badge-darkred">Requires Authentication</small>
</p>

<p>Get details for a specific notification including status and delivery information.</p>

<span id="example-requests-GETapi-v1-notifications--notification_id-">
<blockquote>Example request:</blockquote>
<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "/api/v1/notifications/16" \
    --header "Authorization: Bearer {YOUR_API_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>
<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "/api/v1/notifications/16"
);

const headers = {
    "Authorization": "Bearer {YOUR_API_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>
<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '/api/v1/notifications/16';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_API_TOKEN}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>
</span>

<span id="example-responses-GETapi-v1-notifications--notification_id-">
            <blockquote>
            <p>Example response (200, Success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;title&quot;: &quot;Welcome!&quot;,
        &quot;body&quot;: &quot;Thanks for installing our app.&quot;,
        &quot;status&quot;: &quot;sent&quot;,
        &quot;sent_at&quot;: &quot;2024-01-01T00:00:00.000000Z&quot;,
        &quot;created_at&quot;: &quot;2024-01-01T00:00:00.000000Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Not Found):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Notification not found.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-notifications--notification_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-notifications--notification_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-notifications--notification_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-notifications--notification_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-notifications--notification_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-notifications--notification_id-" data-method="GET"
      data-path="api/v1/notifications/{notification_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-notifications--notification_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-notifications--notification_id-"
                    onclick="tryItOut('GETapi-v1-notifications--notification_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-notifications--notification_id-"
                    onclick="cancelTryOut('GETapi-v1-notifications--notification_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-notifications--notification_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/notifications/{notification_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-notifications--notification_id-"
               value="Bearer {YOUR_API_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_API_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-notifications--notification_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-notifications--notification_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>notification_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="notification_id"                data-endpoint="GETapi-v1-notifications--notification_id-"
               value="16"
               data-component="url">
    <br>
<p>The ID of the notification. Example: <code>16</code></p>
            </div>
                    </form>

                    <h2 id="notifications-DELETEapi-v1-notifications--notification_id-">Cancel a scheduled notification</h2>

<p>
<small class="badge badge-darkred">Requires Authentication</small>
</p>

<p>Cancel a notification that has not been sent yet. Only works for pending or scheduled notifications.</p>

<span id="example-requests-DELETEapi-v1-notifications--notification_id-">
<blockquote>Example request:</blockquote>
<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "/api/v1/notifications/16" \
    --header "Authorization: Bearer {YOUR_API_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>
<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "/api/v1/notifications/16"
);

const headers = {
    "Authorization": "Bearer {YOUR_API_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>
<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '/api/v1/notifications/16';
$response = $client-&gt;delete(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_API_TOKEN}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>
</span>

<span id="example-responses-DELETEapi-v1-notifications--notification_id-">
            <blockquote>
            <p>Example response (200, Success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Notification cancelled successfully.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Already Sent):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Cannot cancel a notification that has already been sent.&quot;,
    &quot;error&quot;: &quot;already_sent&quot;,
    &quot;status&quot;: &quot;sent&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Not Found):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Notification not found.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-v1-notifications--notification_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-notifications--notification_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-notifications--notification_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-notifications--notification_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-notifications--notification_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-notifications--notification_id-" data-method="DELETE"
      data-path="api/v1/notifications/{notification_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-notifications--notification_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-notifications--notification_id-"
                    onclick="tryItOut('DELETEapi-v1-notifications--notification_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-notifications--notification_id-"
                    onclick="cancelTryOut('DELETEapi-v1-notifications--notification_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-notifications--notification_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/notifications/{notification_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-v1-notifications--notification_id-"
               value="Bearer {YOUR_API_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_API_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-notifications--notification_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-v1-notifications--notification_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>notification_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="notification_id"                data-endpoint="DELETEapi-v1-notifications--notification_id-"
               value="16"
               data-component="url">
    <br>
<p>The ID of the notification. Example: <code>16</code></p>
            </div>
                    </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                                                        <button type="button" class="lang-button" data-language-name="php">php</button>
                            </div>
            </div>
</div>

<script>
(function() {
    const tokenInput = document.getElementById('global-auth-token');
    if (!tokenInput) return;

    const storageKey = 'scribe_api_token';

    // Load saved token
    const savedToken = localStorage.getItem(storageKey);
    if (savedToken) {
        tokenInput.value = savedToken;
    }

    // Save token on change
    tokenInput.addEventListener('input', function() {
        localStorage.setItem(storageKey, this.value);
    });

    // Override tryItOut function to auto-fill Authorization
    const originalTryItOut = window.tryItOut;
    if (originalTryItOut) {
        window.tryItOut = function(endpointId) {
            originalTryItOut(endpointId);

            // Auto-fill Authorization header with saved token
            setTimeout(function() {
                const token = tokenInput.value.trim();
                if (token) {
                    const authInput = document.querySelector('#form-' + endpointId + ' input[name="Authorization"]');
                    if (authInput) {
                        authInput.value = 'Bearer ' + token;
                    }
                }
            }, 50);
        };
    }
})();
</script>
</body>
</html>
