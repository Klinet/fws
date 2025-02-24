<?php

namespace Application\Services;

use Doctrine\DBAL\Connection;
use SimpleXMLElement;

class ProductExportService
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function generateXmlFeed(): string
    {
        // Fetch all products with their price history and product packages
        $sql = "
    SELECT p.id, p.title AS product_name, ph.price AS price_history, pp.title AS package_name
    FROM products p
    LEFT JOIN price_history ph ON p.id = ph.product_id
    LEFT JOIN product_package_contents ppc ON p.id = ppc.product_id
    LEFT JOIN product_packages pp ON ppc.product_package_id = pp.id
    ORDER BY p.id, ph.updated_at DESC, pp.title
";

        $products = $this->db->fetchAllAssociative($sql);

        // Initialize SimpleXMLElement
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><products></products>');

        // Organize products by ID
        $organizedProducts = [];
        foreach ($products as $product) {
            $productId = $product['id'];
            if (!isset($organizedProducts[$productId])) {
                $organizedProducts[$productId] = [
                    'product_name' => $product['product_name'],
                    'price' => $product['price'],
                    'price_history' => [],
                    'packages' => []
                ];
            }

            // Add price history if it exists
            if ($product['price_history']) {
                $organizedProducts[$productId]['price_history'][] = $product['price_history'];
            }

            // Add product package information if it exists
            if ($product['package_name']) {
                $organizedProducts[$productId]['packages'][] = $product['package_name'];
            }
        }

        // Add products to the XML
        foreach ($organizedProducts as $productId => $productData) {
            $productElement = $xml->addChild('product');
            $productElement->addChild('title', $productData['product_name']);
            $productElement->addChild('price', $productData['price']);

            // Add price history
            $priceHistoryElement = $productElement->addChild('price_history');
            foreach ($productData['price_history'] as $history) {
                $priceHistoryElement->addChild('price', $history);
            }

            // Add product packages
            $packagesElement = $productElement->addChild('packages');
            foreach ($productData['packages'] as $package) {
                $packagesElement->addChild('package', $package);
            }
        }

        // Return the XML as a string
        return $xml->asXML();
    }
}
