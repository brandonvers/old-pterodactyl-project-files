import React, { useEffect, useState } from 'react';
import PageContentBlock from '@/components/elements/PageContentBlock';
import useFlash from '@/plugins/useFlash';
import tw from 'twin.macro';
import useSWR from 'swr';
import Spinner from '@/components/elements/Spinner';
import GreyRowBox from '@/components/elements/GreyRowBox';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faFilePdf } from '@fortawesome/free-solid-svg-icons';
import styled from 'styled-components/macro';
import { Link } from 'react-router-dom';
import FlashMessageRender from '@/components/FlashMessageRender';
import MessageBox from '@/components/MessageBox';

import getInvoices from '@/api/billing/getInvoices';

const Code = styled.code`${tw`font-mono py-1 px-2 bg-neutral-900 rounded text-sm inline-block`}`;

export interface InvoicesResponse {
    invoices: any[];
}


export default () => {
    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error, mutate } = useSWR<InvoicesResponse>([ '/invoices' ], () => getInvoices());

    useEffect(() => {
        if (!error) {
            clearFlashes('invoices');
        } else {
            clearAndAddHttpError({ key: 'invoices', error });
        }
    });

    return (
        <PageContentBlock title={'Invoices'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full lg:pl-4`}>
                <FlashMessageRender byKey={'invoices'} css={tw`mb-4`} />
            </div>
            {!data ?
                <div css={tw`w-full lg:pl-4`}>
                    <Spinner size={'large'} centered />
                </div>
                :
                <>
                    <div css={tw`w-full lg:pl-4`}>
                        {data.invoices.length < 1 ?
                            <MessageBox type="info" title="Info">
                                There are no invoices.
                            </MessageBox>
                            :
                            (data.invoices.map((item, key) => (
                                <GreyRowBox $hoverable={false} css={tw`mb-2`} key={key}>
                                    <div css={tw`hidden md:block`}>
                                        <FontAwesomeIcon icon={faFilePdf} fixedWidth/>
                                    </div>
                                    <div css={tw`ml-8 text-center`}>
                                        <p css={tw`text-sm`}><Code>#{item.id}</Code></p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>ID</p>
                                    </div>
                                    <div css={tw`flex-1 ml-8`}>
                                        {item.reason === 'Top up Credit' ?
                                        <p css={tw`text-green-500`}>+ <span dangerouslySetInnerHTML={{ __html: item.currency }}></span> {item.amount}</p>
                                        :
                                        <p css={tw`text-red-500`}>- <span dangerouslySetInnerHTML={{ __html: item.currency }}></span> {item.amount}</p>
                                        }
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Amount</p>
                                    </div>
                                    <div css={tw`ml-8 text-center`}>
                                        <p css={tw`text-sm`}>{item.reason}</p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Item Name</p>
                                    </div>
                                    <div css={tw`ml-8 text-center hidden md:block`}>
                                        <p css={tw`text-sm`}>{item.created_at}</p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Date</p>
                                    </div>
                                    <div css={tw`ml-8 text-center hidden md:block`}>
                                        <p css={tw`text-sm text-green-500`}>
                                            <Link to={`/billing/invoices/${item.id}`}>
                                                <FontAwesomeIcon icon={faFilePdf} fixedWidth/> Download
                                            </Link>
                                        </p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Invoice</p>
                                    </div>
                                </GreyRowBox>
                            )))
                        }
                    </div>
                </>
            }
        </PageContentBlock>
    );
};
