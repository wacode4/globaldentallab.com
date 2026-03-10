function buildHeaders() {
  return {
    "Access-Control-Allow-Origin": "*",
    "Cache-Control": "no-store",
    "Content-Type": "application/json",
  };
}

function normalizeText(value, maxLength) {
  const stringValue = typeof value === "string" ? value.trim() : "";

  if (!stringValue) {
    return "";
  }

  return stringValue.slice(0, maxLength);
}

export async function onRequestPost(context) {
  const { request, env } = context;
  const headers = buildHeaders();

  try {
    const data = await request.json();
    const firstName = normalizeText(data.firstName, 80);
    const lastName = normalizeText(data.lastName, 80);
    const email = normalizeText(data.email, 160).toLowerCase();
    const phone = normalizeText(data.phone, 40);
    const clinic = normalizeText(data.clinic, 120);
    const service = normalizeText(data.service, 60);
    const message = normalizeText(data.message, 5000);
    const website = normalizeText(data.website, 255);

    // Quietly swallow obvious bot submissions without creating a record.
    if (website) {
      return new Response(
        JSON.stringify({
          success: true,
          message: "Thank you for your inquiry. We will contact you soon.",
        }),
        { status: 200, headers },
      );
    }

    if (!firstName || !lastName || !email || !message) {
      return new Response(
        JSON.stringify({ success: false, error: "Missing required fields" }),
        { status: 400, headers },
      );
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      return new Response(
        JSON.stringify({ success: false, error: "Invalid email format" }),
        { status: 400, headers },
      );
    }

    if (!env.DB) {
      return new Response(
        JSON.stringify({ success: false, error: "Database is not configured" }),
        { status: 500, headers },
      );
    }

    const result = await env.DB.prepare(
      `
        INSERT INTO inquiries (first_name, last_name, email, phone, clinic, service, message)
        VALUES (?, ?, ?, ?, ?, ?, ?)
      `,
    )
      .bind(
        firstName,
        lastName,
        email,
        phone || null,
        clinic || null,
        service || null,
        message,
      )
      .run();

    return new Response(
      JSON.stringify({
        success: true,
        message: "Thank you for your inquiry. We will contact you soon.",
        id: result.meta.last_row_id,
      }),
      { status: 200, headers },
    );
  } catch (error) {
    console.error("Contact form error:", error);
    return new Response(
      JSON.stringify({
        success: false,
        error: "Server error. Please try again.",
      }),
      { status: 500, headers },
    );
  }
}

export async function onRequestOptions() {
  return new Response(null, {
    headers: {
      ...buildHeaders(),
      "Access-Control-Allow-Methods": "POST, OPTIONS",
      "Access-Control-Allow-Headers": "Content-Type",
    },
  });
}
