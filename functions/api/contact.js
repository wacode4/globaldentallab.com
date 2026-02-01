export async function onRequestPost(context) {
  const { request, env } = context;

  // CORS headers
  const headers = {
    "Access-Control-Allow-Origin": "*",
    "Content-Type": "application/json",
  };

  try {
    const data = await request.json();

    // Validate required fields
    const { firstName, lastName, email, message } = data;
    if (!firstName || !lastName || !email || !message) {
      return new Response(
        JSON.stringify({ success: false, error: "Missing required fields" }),
        { status: 400, headers },
      );
    }

    // Basic email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      return new Response(
        JSON.stringify({ success: false, error: "Invalid email format" }),
        { status: 400, headers },
      );
    }

    // Insert into database
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
        data.phone || null,
        data.clinic || null,
        data.service || null,
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

// Handle CORS preflight
export async function onRequestOptions() {
  return new Response(null, {
    headers: {
      "Access-Control-Allow-Origin": "*",
      "Access-Control-Allow-Methods": "POST, OPTIONS",
      "Access-Control-Allow-Headers": "Content-Type",
    },
  });
}
