export async function onRequestGet(context) {
  const { request, env } = context;

  const headers = {
    "Access-Control-Allow-Origin": "*",
    "Content-Type": "application/json",
  };

  const url = new URL(request.url);
  const key = url.searchParams.get("key");
  if (key !== "gdl2026") {
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
        totalInquiries: inquiryCount.count,
        newInquiries: newInquiries.count,
        activeSubscriptions: subCount.count,
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
