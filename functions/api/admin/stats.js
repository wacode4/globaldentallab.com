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
    const inquiryCount = await env.DB.prepare(
      "SELECT COUNT(*) as count FROM inquiries",
    ).first();
    const newInquiries = await env.DB.prepare(
      "SELECT COUNT(*) as count FROM inquiries WHERE status = 'new'",
    ).first();
    const subCount = await env.DB.prepare(
      "SELECT COUNT(*) as count FROM subscriptions WHERE status = 'active'",
    ).first();

    return new Response(
      JSON.stringify({
        totalInquiries: Number(inquiryCount.count || 0),
        newInquiries: Number(newInquiries.count || 0),
        activeSubscriptions: Number(subCount.count || 0),
      }),
      { status: 200, headers },
    );
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
