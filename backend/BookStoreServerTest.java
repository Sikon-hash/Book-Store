import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.junit.jupiter.api.Assertions.assertTrue;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.AfterEach;

import com.sun.net.httpserver.Headers;
import com.sun.net.httpserver.HttpExchange;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.URI;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.nio.file.StandardCopyOption;

public class BookStoreServerTest {

    @BeforeEach
    void setUp() throws Exception {
        Files.copy(Paths.get("tests/seed/products.json"), Paths.get("../data/products.json"), StandardCopyOption.REPLACE_EXISTING);
        Files.copy(Paths.get("tests/seed/orders.json"), Paths.get("../data/orders.json"), StandardCopyOption.REPLACE_EXISTING);
        Files.copy(Paths.get("tests/seed/users.json"), Paths.get("../data/users.json"), StandardCopyOption.REPLACE_EXISTING);
        System.out.println("[setUp] Data restored from tests/seed/");
    }

    @AfterEach
    void tearDown() throws Exception {
        Files.writeString(Paths.get("../data/orders.json"), "[]");
        System.out.println("[tearDown] orders.json cleaned");
    }

    @Test
    void testCalculateTotal_quantityZero() {
        assertEquals(0.0, BookStoreServer.calculateTotal(0, 100000), 0.001);
    }

    @Test
    void testCalculateTotal_quantityOneToNine_noDiscount() {
        assertEquals(300000.0, BookStoreServer.calculateTotal(3, 100000), 0.001);
    }

    @Test
    void testCalculateTotal_quantityTenToFortyNine_tenPercentDiscount() {
        double expected = 10 * 100000 * 0.9;
        assertEquals(expected, BookStoreServer.calculateTotal(10, 100000), 0.001);
    }

    @Test
    void testCalculateTotal_quantityFiftyToNinetyNine_lowPrice_fifteenPercentDiscount() {
        double expected = 50 * 80000 * 0.85;
        assertEquals(expected, BookStoreServer.calculateTotal(50, 80000), 0.001);
    }

    @Test
    void testCalculateTotal_quantityFiftyToNinetyNine_highPrice_twentyPercentDiscount() {
        double expected = 50 * 150000 * 0.80;
        assertEquals(expected, BookStoreServer.calculateTotal(50, 150000), 0.001);
    }

    @Test
    void testCalculateTotal_quantityHundredPlus_lowPrice_twentyFivePercentDiscount() {
        double expected = 100 * 90000 * 0.75;
        assertEquals(expected, BookStoreServer.calculateTotal(100, 90000), 0.001);
    }

    @Test
    void testCalculateTotal_quantityHundredPlus_highPrice_thirtyPercentDiscount() {
        double expected = 100 * 200000 * 0.70;
        assertEquals(expected, BookStoreServer.calculateTotal(100, 200000), 0.001);
    }

    @Test
    void testGetBasePrice_validProductId() {
        double price = BookStoreServer.getBasePrice("B001");
        assertTrue(price > 0);
    }

    @Test
    void testGetBasePrice_invalidProductId() {
        assertEquals(0.0, BookStoreServer.getBasePrice("INVALID"), 0.001);
    }

    @Test
    void testExtractJsonString_validKey() {
        String json = "{\"id\":\"B001\",\"name\":\"test\"}";
        assertEquals("B001", BookStoreServer.extractJsonString(json, "id"));
    }

    @Test
    void testExtractJsonString_missingKey() {
        String json = "{\"name\":\"test\"}";
        assertEquals("", BookStoreServer.extractJsonString(json, "id"));
    }

    @Test
    void testExtractJsonInt_validKey() {
        String json = "{\"quantity\":5,\"price\":1000}";
        assertEquals(5, BookStoreServer.extractJsonInt(json, "quantity"));
    }

    @Test
    void testExtractJsonInt_missingKey() {
        String json = "{\"name\":\"test\"}";
        assertEquals(0, BookStoreServer.extractJsonInt(json, "quantity"));
    }

    @Test
    void testCalculateHandler_postRequest() throws Exception {
        MockHttpExchange exchange = new MockHttpExchange(
            "POST", "{\"product_id\":\"B001\",\"quantity\":2}"
        );

        BookStoreServer.CalculateHandler handler = new BookStoreServer.CalculateHandler();
        handler.handle(exchange);

        String response = exchange.getResponseBodyContent();
        assertTrue(response.contains("\"total\""));
    }

    @Test
    void testCalculateHandler_nonPostReturns405() throws Exception {
        MockHttpExchange exchange = new MockHttpExchange(
            "GET", ""
        );

        BookStoreServer.CalculateHandler handler = new BookStoreServer.CalculateHandler();
        handler.handle(exchange);

        assertEquals(405, exchange.getStatusCode());
    }

    static class MockHttpExchange extends HttpExchange {
        private final String method;
        private final String requestBody;
        private final Headers responseHeaders = new Headers();
        private int responseCode;
        private long responseLength;
        private final ByteArrayOutputStream responseBody = new ByteArrayOutputStream();

        MockHttpExchange(String method, String requestBody) {
            this.method = method;
            this.requestBody = requestBody;
        }

        @Override
        public String getRequestMethod() { return method; }

        @Override
        public InputStream getRequestBody() {
            return new ByteArrayInputStream(requestBody.getBytes());
        }

        @Override
        public Headers getResponseHeaders() { return responseHeaders; }

        @Override
        public Headers getRequestHeaders() { return new Headers(); }

        @Override
        public void sendResponseHeaders(int code, long length) {
            this.responseCode = code;
            this.responseLength = length;
        }

        @Override
        public OutputStream getResponseBody() { return responseBody; }

        String getResponseBodyContent() { return responseBody.toString(); }
        int getStatusCode() { return responseCode; }
        @Override public int getResponseCode() { return responseCode; }

        @Override public URI getRequestURI() { throw new UnsupportedOperationException(); }
        @Override public String getProtocol() { throw new UnsupportedOperationException(); }
        @Override public com.sun.net.httpserver.HttpContext getHttpContext() { throw new UnsupportedOperationException(); }
        @Override public void close() {}
        @Override public Object getAttribute(String n) { return null; }
        @Override public void setAttribute(String n, Object v) {}
        @Override public void setStreams(InputStream i, OutputStream o) {}
        @Override public java.net.InetSocketAddress getRemoteAddress() { return null; }
        @Override public java.net.InetSocketAddress getLocalAddress() { return null; }
        @Override public com.sun.net.httpserver.HttpPrincipal getPrincipal() { return null; }
    }
}
