<?php

declare(strict_types=1);

namespace PcmtCoreBundle\Connector\Mapping;

class E2OpenMapping
{
    public static function findMappingForKey(string $key): ?string
    {
        if (!array_key_exists($key, self::mapping())) {
            return null;
        }

        return self::mapping()[$key];
    }

    /**
     * @return bool|string|null
     */
    public static function mapValue(?string $value)
    {
        $mapping = [
            'true'  => 1,
            'false' => 0,
        ];

        return $mapping[$value] ?? $value;
    }

    private static function mapping(): array
    {
        return [
            '{}additionalTradeItemIdentification'                => 'GS1_ADDITIONALTRADEITEMIDENTIFICATION',
            '{}isTradeItemABaseUnit'                             => 'GS1_ISTRADEITEMABASEUNIT',
            '{}isTradeItemAConsumerUnit'                         => 'GS1_ISTRADEITEMACONSUMERUNIT',
            '{}gtin'                                             => 'GTIN',
            '{}isTradeItemADespatchUnit'                         => 'GS1_ISTRADEITEMADESPATCHUNIT',
            '{}isTradeItemAnInvoiceUnit'                         => 'GS1_ISTRADEITEMANINVOICEUNIT',
            '{}isTradeItemAnOrderableUnit'                       => 'GS1_ISTRADEITEMANORDERABLEUNIT',
            '{}isTradeItemAService'                              => '',
            '{}isTradeItemNonphysical'                           => '',
            '{}tradeItemUnitDescriptorCode'                      => 'GS1_TRADEITEMUNITDESCRIPTORCODE',
            '{}brandOwner{}gln'                                  => 'GS1_BRANDOWNERGLN',
            '{}manufacturerOfTradeItem{}gln'                     => 'GS1_MANUFACTUREROFTRADEITEMGLN',
            '{}informationProviderOfTradeItem{}gln'              => 'GS1_INFORMATIONPROVIDEROFTRADEITEMGLN',
            '{}brandOwner{}partyName'                            => 'GS1_BRANDOWNERPARTYNAME',
            '{}informationProviderOfTradeItem{}partyName'        => 'GS1_INFORMATIONPROVIDEROFTRADEITEMPARTYNAME',
            '{}manufacturerOfTradeItem{}partyName'               => 'GS1_MANUFACTUREROFTRADEITEMPARTYNAME',
            '{}manufacturerOfTradeItem{}partyAddress'            => 'GS1_MANUFACTUREROFTRADEITEMPARTYADDRESS',
            '{}gpcCategoryCode'                                  => 'GS1_GPCCATEGORYCODE',
            '{}gpcCategoryDefinition'                            => '',
            '{}gpcCategoryName'                                  => '',
            '{}additionalTradeItemClassificationSystemCode'      => '',
            '{}additionalTradeItemClassificationCodeValue'       => 'GS1_ADDITIONALTRADEITEMCLASSIFICATIONCODEVALUE',
            '{}additionalTradeItemClassificationVersion'         => 'GS1_ADDITIONALTRADEITEMCLASSIFICATIONVERSION',
            '{}targetMarketCountryCode'                          => 'GS1_TARGETMARKETCOUNTRYCODE',
            '{}doesItemContainAControlledSubstance'              => '',
            '{}controlledSubstanceScheduleCodeReference'         => 'GS1_CONTROLLEDSUBSTANCESCHEDULECODEREFERENCE',
            '{}controlledSubstanceAmount'                        => 'GS1_CONTROLLEDSUBSTANCEAMOUNT',
            '{}controlledSubstanceCode'                          => '',
            '{}controlledSubstanceName'                          => 'GS1_CONTROLLEDSUBSTANCENAME',
            '{}isDangerousSubstance'                             => 'GS1_ISDANGEROUSSUBSTANCE',
            '{}startAvailabilityDateTime'                        => 'GS1_STARTAVAILABILITYDATETIME',
            '{}externalAgencyName'                               => 'GS1_EXTERNALAGENCYNAME',
            '{}externalCodeListName'                             => 'GS1_EXTERNALCODELISTNAME',
            '{}enumerationValue'                                 => 'GS1_ENUMERATIONVALUE',
            '{}enumerationValueDescription'                      => 'GS1_ENUMERATIONVALUEDESCRIPTION',
            '{}tradeItemFeatureBenefit'                          => '',
            '{}tradeItemMarketingMessage'                        => '',
            '{}packagingShapeCode'                               => '',
            '{}packagingTypeCode'                                => 'GS1_PACKAGINGTYPECODE',
            '{}packagingMaterialTypeCode'                        => '',
            '{}dosageFormTypeCodeReference'                      => '',
            '{}countryCode'                                      => 'GS1_PLACEOFPRODUCTACTIVITYCOUNTRYOFORIGINCOUNTRYCODE',
            '{}referencedFileTypeCode'                           => 'GS1_REFERENCEDFILETYPECODE',
            '{}fileEffectiveStartDateTime'                       => '',
            '{}uniformResourceIdentifier'                        => 'GS1_UNIFORMRESOURCEIDENTIFIER',
            '{}regulationTypeCode'                               => 'GS1_REGULATORYINFORMATIONREGULATIONTYPECODE',
            '{}regulatoryAct'                                    => '',
            '{}regulatoryAgency'                                 => '',
            '{}regulationRestrictionsAndDescriptors'             => 'GS1_REGULATIONRESTRICTIONSANDDESCRIPTORS',
            '{}permitEndDateTime'                                => 'GS1_PERMITENDDATETIME',
            '{}permitStartDateTime'                              => 'GS1_PERMITSTARTDATETIME',
            '{}regulatoryPermitIdentification'                   => 'GS1_REGULATORYPERMITIDENTIFICATION',
            '{}isRegulatedForTransportation'                     => 'GS1_SAFETYDATASHEETINFORMATIONISREGULATEDFORTRANSPORTATION',
            '{}gs1TradeItemIdentificationKeyCode'                => 'GS1_GS1TRADEITEMIDENTIFICATIONKEYCODE',
            '{}gs1TradeItemIdentificationKeyValue'               => 'GS1_GS1TRADEITEMIDENTIFICATIONKEYVALUE',
            '{}dataCarrierTypeCode'                              => 'GS1_DATACARRIERTYPECODE',
            '{}descriptionShort'                                 => 'GS1_DESCRIPTIONSHORT',
            '{}descriptionShortlanguageCode'                     => 'GS1_DESCRIPTIONSHORT_LANGUAGECODE',
            '{}functionalName'                                   => 'GS1_FUNCTIONALNAME',
            '{}functionalNamelanguageCode'                       => 'GS1_FUNCTIONALNAME_LANGUAGECODE',
            '{}tradeItemDescription'                             => 'GS1_TRADEITEMDESCRIPTION',
            '{}brandName'                                        => 'GS1_BRANDNAME',
            '{}handlingInstructionsCodeReference'                => 'GS1_HANDLINGINSTRUCTIONCODEREFERENCE',
            '{}minimumTradeItemLifespanFromTimeOfProduction'     => 'GS1_MINIMUMTRADEITEMLIFESPANFROMTIMEOFPRODUCTION',
            '{}depth'                                            => 'GS1_DEPTH',
            '{}depthmeasurementUnitCode'                         => 'GS1_DEPTH_MEASUREMENTUNITCODE',
            '{}height'                                           => 'GS1_HEIGHT',
            '{}heightmeasurementUnitCode'                        => 'GS1_HEIGHT_MEASUREMENTUNITCODE',
            '{}inBoxCubeDimension'                               => 'GS1_INBOXCUBEDIMENSION',
            '{}netContent'                                       => 'GS1_NETCONTENT',
            '{}netContentmeasurementUnitCode'                    => 'GS1_NETCONTENT_MEASUREMENTUNITCODE',
            '{}width'                                            => 'GS1_WIDTH',
            '{}widthmeasurementUnitCode'                         => 'GS1_WIDTH_MEASUREMENTUNITCODE',
            '{}grossWeight'                                      => 'GS1_GROSSWEIGHT',
            '{}grossWeightmeasurementUnitCode'                   => 'GS1_GROSSWEIGHT_MEASUREMENTUNITCODE',
            '{}netWeight'                                        => 'GS1_NETWEIGHT',
            '{}maximumTemperature'                               => 'GS1_MAXIMUMTEMPERATURE',
            '{}maximumTemperaturetemperatureMeasurementUnitCode' => 'GS1_MAXIMUMTEMPERATURE_MEASUREMENTUNITCODE',
            '{}minimumTemperature'                               => 'GS1_MINIMUMTEMPERATURE',
            '{}minimumTemperaturetemperatureMeasurementUnitCode' => 'GS1_MINIMUMTEMPERATURE_MEASUREMENTUNITCODE',
            '{}temperatureQualifierCode'                         => '',
            '{}isTradeItemAVariableUnit'                         => 'GS1_IISTRADEITEMAVARIABLEUNIT',
            '{}value'                                            => '',
            '{}attr'                                             => '',
            '{}lastChangeDateTime'                               => '',
            '{}effectiveDateTime'                                => '',
            '{}publicationDateTime'                              => '',
        ];
    }
}