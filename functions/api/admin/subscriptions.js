function buildHeaders() {
  return {
    "Access-Control-Allow-Origin": "*",
    "Cache-Control": "no-store",
    "Content-Type": "application/json",
  };
}

function getAdminKey(request, env) {
  const url = new URL(request.url);
  const headerKey = request.headers.get("x-admin-key");
  const queryKey = url.searchParams.get("key");
  const configuredKey = (env.ADMIN_KEY || "gdl2026").trim();

  return {
    configuredKey,
    providedKey: (headerKey || queryKey || "").trim(),
  };
}

export async function onRequestGet(context) {
  const { request, env } = context;
  const headers = buildHeaders();
  const { configuredKey, providedKey } = getAdminKey(request, env);

  if (!providedKey || providedKey !== configuredKey) {
    return new Response(JSON.stringify({ error: "Unauthorized" }), {
      status: 401,
      headers,
    });
  }

  try {
    const results = await env.DB.prepare(
      "SELECT id, email, created_at, status FROM subscriptions ORDER BY created_at DESC",
    ).all();

    return new Response(JSON.stringify(results.results), {
      status: 200,
      headers,
    });
  } catch (error) {
    return new Response(JSON.stringify({ error: error.message }), {
      status: 500,
      headers,
    });
  }
}

export async function onRequestOptions() {
  return new Response(null, {
    headers: {
      ...buildHeaders(),
      "Access-Control-Allow-Methods": "GET, OPTIONS",
      "Access-Control-Allow-Headers": "Content-Type, X-Admin-Key",
    },
  });
}
